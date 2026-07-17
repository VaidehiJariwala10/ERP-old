<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\PaymentStore;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\StaffDepartmentScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PurchaseController extends Controller
{
       private function resolveFinancialYearRange(?string $financialYear): ?array
    {
        if (empty($financialYear)) {
            return null;
        }

        if (!preg_match('/^(\d{4})-(\d{4})$/', trim($financialYear), $matches)) {
            return null;
        }

        $startYear = (int) $matches[1];
        $endYear = (int) $matches[2];

        if ($endYear !== $startYear + 1) {
            return null;
        }

        $from = Carbon::create($startYear, 4, 1)->startOfDay()->toDateTimeString();
        $to = Carbon::create($endYear, 3, 31)->endOfDay()->toDateTimeString();

        return [$from, $to];
    }

    private function applyFinancialYearFilter($query, ?string $financialYear, string $column = 'created_at'): void
    {
        $range = $this->resolveFinancialYearRange($financialYear);
        if (!$range) {
            return;
        }

        $query->whereBetween($column, $range);
    }

    private function normalizeMoney($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($normalized === null || $normalized === '' || $normalized === '-' || $normalized === '.' || $normalized === '-.') {
            return 0.0;
        }

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }

 private function resolvePurchaseRemainingAmount($invoiceRemainingAmount, $paymentRemainingAmount = null): float
    {
        if ($paymentRemainingAmount !== null && $paymentRemainingAmount !== '') {
            return max(0, $this->normalizeMoney($paymentRemainingAmount));
        }

        return max(0, $this->normalizeMoney($invoiceRemainingAmount));
    }


    public function purchase_order(Request $request)
    {
        // dd($request->all());
        $user         = Auth::guard('api')->user();
        $userId       = $user->id;
        $userBranchId = $user->branch_id;
        $role         = $user->role;

        if ($role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif ($role === 'admin' && ! empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $validator = Validator::make($request->all(), [
            'vendor_id'              => 'required',
            'status'                 => 'nullable|in:pending,completed,cancelled',
            'vendor_phone'           => 'nullable|numeric',
            'discount'               => 'nullable|numeric',
            'shipping'               => 'required|numeric',
            'bill_no' => [
                'required',
                Rule::unique('purchase_invoice', 'bill_no')
                    ->where('branch_id', $userBranchId)
                    ->where('isDeleted', 0),
            ],
            'purchase_date'          => 'required|date',
            'remark'                 => 'nullable|string',
            'grand_total'            => 'required|numeric',
            'products'               => 'required|array|min:1',
            'products.*.id'          => 'required',
            'products.*.category_id' => 'required',
            'products.*.price'       => 'required|numeric|min:0',
            'products.*.quantity'    => 'required|numeric|min:1',
            'products.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'products.*.discount_amount'  => 'nullable|numeric|min:0',
            'products.*.total'       => 'required|numeric|min:0',
            'taxes'                  => 'nullable|array',
            'taxes.*.id'             => 'required|numeric',
            'taxes.*.name'           => 'required|string',
            'taxes.*.rate'           => 'required|numeric',
            'taxes.*.amount'         => 'required|numeric',
            'bank_id'                => 'required_if:payment_mode,online,cashonline|nullable|exists:bank_master,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('products', []) as $index => $product) {
                $discountPercent = (float) ($product['discount_percent'] ?? 0);
                $discountAmount  = (float) ($product['discount_amount'] ?? 0);
                $lineTotal       = (float) ($product['total'] ?? 0);
                $lineGross       = $lineTotal + $discountAmount;

                if ($discountPercent > 100) {
                    $validator->errors()->add(
                        "products.{$index}.discount_percent",
                        'Discount percent cannot exceed 100%.'
                    );
                }

                if ($discountAmount < 0) {
                    $validator->errors()->add(
                        "products.{$index}.discount_amount",
                        'Discount amount cannot be negative.'
                    );
                }

                if ($lineGross > 0 && $discountAmount - $lineGross > 0.01) {
                    $validator->errors()->add(
                        "products.{$index}.discount_amount",
                        'Discount amount cannot exceed line total.'
                    );
                }

                if ($lineTotal < 0) {
                    $validator->errors()->add(
                        "products.{$index}.total",
                        'Product total cannot be negative.'
                    );
                }
            }

            if ((float) $request->input('grand_total', 0) < 0) {
                $validator->errors()->add('grand_total', 'Grand total cannot be negative.');
            }

            // IMEI unique validation
            $allImeis = []; // Check duplicates within the same form
            foreach ($request->input('products', []) as $index => $product) {
                $imeiNoJson = $product['imei_no'] ?? null;
                if (!empty($imeiNoJson)) {
                    $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                    if (is_array($imeiArray)) {
                        foreach ($imeiArray as $imeiIndex => $imei) {
                            if (!empty($imei)) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add(
                                        "imei_error_{$index}_{$imeiIndex}",
                                        "IMEI {$imei} is duplicated in this form."
                                    );
                                }
                                $allImeis[] = $imei;
                                
                                $exists = \DB::table('purchases')
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($exists) {
                                    $validator->errors()->add(
                                        "imei_error_{$index}_{$imeiIndex}",
                                        "IMEI {$imei} already exists in the system."
                                    );
                                }
                            }
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ✅ Generate a unique invoice number
            do {
                $invoice_number = 'INV-' . mt_rand(10000000, 99999999);
            } while (PurchaseInvoice::where('invoice_number', $invoice_number)->exists());

            // ✅ Check and insert vendor
            if (! is_numeric($request->vendor_id)) {
                // Check if phone already exists
                $vendorPhone = $request->vendor_phone;

                if ($vendorPhone !== null && $vendorPhone !== '') {
                    // Check if phone already exists
                    $existingVendor = User::where('phone', $vendorPhone)->first();

                    if ($existingVendor) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A vendor with this phone number already exists.',
                        ], 409);
                    }
                }
                $vendor = User::create([
                    'name'       => $request->vendor_id,
                    'phone'      => $request->vendor_phone ?? null,
                    'role'       => 'vendor',
                    'status'     => $request->status,
                    'branch_id'  => $userBranchId ?? $userId,
                    'created_by' => $userId,
                ]);
                $vendor_id = $vendor->id;

                // ✅ Create UserDetail record after user is created
                UserDetail::create([
                    'user_id'   => $vendor_id,
                    'address'   => '', // adjust if you collect address info
                    'city'      => '',
                    'state'     => '',
                    'country'   => '',
                    'pincode'   => '',
                    'branch_id' => $userBranchId ?? $userId,
                ]);
            } else {
                $vendor_id = $request->vendor_id;
            }

            // ✅ OPTIMIZED: Process Products with bulk operations
            $processedProducts = [];
            $productIds        = array_filter(array_column($request->products, 'id'), 'is_numeric');

            // ✅ OPTIMIZED: Bulk load existing products
            $existingProducts = ! empty($productIds)
                ? Product::whereIn('id', $productIds)->get()->keyBy('id')
                : collect();

            // ✅ OPTIMIZED: Get all last inventories in one query
            $lastInventories = ! empty($productIds)
                ? ProductInventory::whereIn('product_id', $productIds)
                ->orderBy('product_id')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('product_id')
                ->map(function ($group) {
                    return $group->first();
                })
                : collect();

            $purchasesData  = [];
            $inventoryData  = [];
            $productUpdates = [];
            $now            = now();

            foreach ($request->products as $product) {
                // Handle Category
                if (! is_numeric($product['category_id'])) {
                    $category    = Category::create([
                        'name' => $product['category_id'],
                        'branch_id' => $userBranchId ?? $userId
                    ]);
                    $category_id = $category->id;
                } else {
                    $category_id = $product['category_id'];
                }

                if (! is_numeric($product['id'])) {
                    // New product
                    do {
                        $sku = mt_rand(10000000, 99999999);
                    } while (Product::where('SKU', $sku)->exists());

                    $newProduct = Product::create([
                        'name'          => $product['id'],
                        'category_id'   => $category_id,
                        'price'         => $product['price'],
                        'quantity'      => $product['quantity'],
                        'vendor_id'     => $vendor_id,
                        'availablility' => 'in_stock',
                        'status'        => 'active',
                        'SKU'           => $sku,
                        'branch_id'     => $userBranchId ?? $userId,
                    ]);
                    $product_id = $newProduct->id;
                } else {
                    // Existing product
                    $existingProduct = $existingProducts->get($product['id']);
                    if ($existingProduct) {
                        $newQuantity                          = $existingProduct->quantity + $product['quantity'];
                        $productUpdates[$existingProduct->id] = [
                            'quantity'    => $newQuantity,
                            'category_id' => $category_id,
                        ];
                        $product_id = $existingProduct->id;
                    } else {
                        continue; // Skip if product not found
                    }
                }

                $gst_details = [];
                $gst_total   = 0;
                $baseTotal   = $product['price'] * $product['quantity'];
                $discount_amount = $product['discount_amount'] ?? 0;
                $taxableAmount   = $baseTotal;

                if ($request->gst_option === 'with') {
                    $prod = $existingProducts->get($product_id);
                    if ($prod && $prod->gst_option === 'with_gst' && $prod->product_gst) {
                        $taxes = json_decode($prod->product_gst, true);
                        if (is_array($taxes)) {
                            foreach ($taxes as $tax) {
                                $taxRate   = floatval($tax['tax_rate'] ?? 0);
                                $taxAmount = ($taxableAmount * $taxRate) / 100;
                                $gst_total += $taxAmount;
                                $gst_details[] = [
                                    'name'   => $tax['tax_name'] ?? '',
                                    'rate'   => $taxRate,
                                    'amount' => $taxAmount,
                                ];
                            }
                        }
                    }
                }

                $processedProducts[] = [
                    'product_id'          => $product_id,
                    'price'               => $product['price'],
                    'quantity'            => $product['quantity'],
                    'discount_percent'    => $product['discount_percent'] ?? 0,
                    'discount_amount'     => $product['discount_amount'] ?? 0,
                    'total'               => $product['total'],
                    'imei_no'             => $product['imei_no'] ?? null,
                    'product_gst_total'   => $gst_total,
                    'product_gst_details' => $gst_details,
                ];
            }
            $discountAmount = $this->normalizeMoney($request->discount ?? 0);
            $shippingAmount = $this->normalizeMoney($request->shipping ?? 0);
            $grandTotal     = $this->normalizeMoney($request->grand_total ?? 0);
            $directAmount   = $this->normalizeMoney($request->amount ?? 0);
            $cashInput      = $this->normalizeMoney($request->cash_amount ?? 0);
            $upiInput       = $this->normalizeMoney($request->upi_amount ?? 0);
            $purchaseDate   = Carbon::parse($request->purchase_date . ' ' . now()->format('H:i:s'));

            // Determine remaining amount
            $remainingAmount = 0;

            if (empty($request->payment_mode)) {
                // No payment made yet
                $remainingAmount = $grandTotal;
            } elseif (($request->paid_type ?? 'full') === 'full') {
                // Full payment done → no remaining amount
                $remainingAmount = 0;
            } else {
                // Partial payment → remaining = grand_total - paid_amount
                $paidAmount = $directAmount > 0 ? $directAmount : ($cashInput + $upiInput);

                $remainingAmount = max(0, $grandTotal - $paidAmount);
            }

            // ✅ Store purchase invoice
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number'   => $invoice_number,
                'vendor_id'        => $vendor_id,
                'products'         => json_encode($processedProducts),
                'total_amount'     => collect($processedProducts)->sum('total'),
                'discount'         => $discountAmount,
                'shipping'         => $shippingAmount,
                'grand_total'      => $grandTotal,
                'remaining_amount' => $remainingAmount,
                'gst_option'       => $request->gst_option === 'with' ? 'with_gst' : 'without_gst',
                'status'           => $request->status,
                'taxes'            => json_encode($request->taxes),
                'branch_id'        => $userBranchId ?? $userId,
                'created_by'       => $userId,
                'bill_no'          => $request->bill_no,
                'purchase_date'    => $purchaseDate,
                'remark'           => $request->remark,
                'updated_at'       => now(),
            ]);

            // ✅ Determine overall payment status dynamically
            // $paymentStatus = 'unpaid';

            // if (! empty($request->payment_mode)) {

            //     if (($request->paid_type ?? 'full') === 'full') {
            //         $paymentStatus = 'paid';
            //     } else {
            //         $paidAmount = $request->amount ?? (($request->upi_amount ?? 0) + ($request->cash_amount ?? 0));

            //         if ($paidAmount > 0 && $paidAmount < $request->grand_total) {
            //             $paymentStatus = 'partial';
            //         }
            //     }
            // }
            // ✅ Default values
            $purchaseStatus = 'pending';
            $paymentStatus  = 'pending';

            // ✅ If payment mode exists
            if (! empty($request->payment_mode)) {

                // FULL PAYMENT → COMPLETED + COMPLETED
                if (($request->paid_type ?? 'full') === 'full') {
                    $purchaseStatus = 'completed';
                    $paymentStatus  = 'completed';
                }

                // PARTIAL PAYMENT
                elseif (($request->paid_type ?? '') === 'partial') {
                    $purchaseStatus = 'pending';
                    $paymentStatus  = 'partially';
                }
            }

            // ✅ Pending payment mode (no payment done)
            if ($request->payment_mode === 'pending') {
                $purchaseStatus = 'pending';
                $paymentStatus  = 'pending';
            }

            // dd($paymentStatus);
            // ✅ OPTIMIZED: Bulk insert purchases and inventory
            foreach ($processedProducts as $item) {
                $product_id   = $item['product_id'];
                $price        = $item['price'];
                $quantity     = $item['quantity'];
                $baseTotal    = $price * $quantity;
                $gst_total = $item['product_gst_total'] ?? 0;
                $gst_details = $item['product_gst_details'] ?? [];

                $purchasesData[] = [
                    'invoice_id'          => $purchaseInvoice->id,
                    'item'                => $product_id,
                    'quantity'            => $quantity,
                    'price'               => $price,
                    'discount_percent'    => $item['discount_percent'] ?? 0,
                    'discount_amount'     => $item['discount_amount'] ?? 0,
                    'amount_total'        => $item['total'],
                    'product_gst_details' => json_encode($gst_details),
                    'product_gst_total'   => $gst_total,
                    'imei_no'             => $item['imei_no'] ?? null,
                    'vendor_id'           => $vendor_id,
                    'purchase_status'     => $purchaseStatus,
                    'payment_status'      => $paymentStatus,
                    'branch_id'           => $userBranchId ?? $userId,
                    'created_by'          => $userId,
                    'created_at'          => $purchaseDate,
                    'updated_at'          => $now,
                ];

                // Prepare inventory data
                $lastInventory = $lastInventories->get($item['product_id']);
                $currentStock  = $lastInventory
                    ? ($lastInventory->current_stock + $item['quantity'])
                    : $item['quantity'];

                $inventoryData[] = [
                    'product_id'    => $item['product_id'],
                    'initial_stock' => $lastInventory ? $lastInventory->current_stock : 0,
                    'current_stock' => $currentStock,
                    'branch_id'     => $userBranchId ?? $userId,
                    'create_by'     => $userId,
                    'type'          => 'Purchase',
                    'date'          => $purchaseDate,
                    'created_at'    => $purchaseDate,
                    'updated_at'    => $now,
                ];
            }

            // ✅ OPTIMIZED: Bulk insert purchases
            if (! empty($purchasesData)) {
                Purchases::insert($purchasesData);
            }

            // ✅ OPTIMIZED: Bulk update products
            foreach ($productUpdates as $productId => $updateData) {
                Product::where('id', $productId)->update($updateData);
            }

            // ✅ OPTIMIZED: Bulk insert inventory records
            if (! empty($inventoryData)) {
                ProductInventory::insert($inventoryData);
            }

            // ✅ Payment handling
            if (! empty($request->payment_mode)) {
                $paymentMode = strtolower($request->payment_mode);
                $paidType    = strtolower($request->paid_type ?? 'full');
                $cashAmount  = $cashInput;
                // $upiAmount   = (float) ($request->upi_amount ?? 0);
                $upiAmount  = $upiInput;

                if ($paymentMode === 'cash') {
                    $cashAmount = $cashAmount > 0 ? $cashAmount : $directAmount;
                    $upiAmount  = 0;
                } elseif ($paymentMode === 'online') {
                    $upiAmount  = $upiAmount > 0 ? $upiAmount : $directAmount;
                    $cashAmount = 0;
                }

                // ✅ Calculate total paid and pending
                $totalPaid = $cashAmount + $upiAmount;
                $pending   = max(0, $grandTotal - $totalPaid); // prevents negative

                // ✅ CASE 1: Cash + Online (both)
                if ($paymentMode === 'cashonline') {
                    $payments = [];

                    // 1️⃣ Insert Cash Payment
                    if ($cashAmount > 0) {
                        $payments[] = PaymentStore::create([
                            'user_id'          => $vendor_id,
                            'purchase_id'      => $purchaseInvoice->id,
                            'payment_amount'   => $cashAmount,
                            'payment_date'     => now(),
                            'payment_method'   => 'Cash',
                            'payment_type'     => $paidType,
                            'cash_amount'      => $cashAmount,
                            'upi_amount'       => 0,
                            'remaining_amount' => $pending,
                            'status'           => 'debit', // ✅ ADD THIS
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                        ]);
                    }

                    // 2️⃣ Insert Online Payment
                    if ($upiAmount > 0) {
                        $payments[] = PaymentStore::create([
                            'user_id'          => $vendor_id,
                            'purchase_id'      => $purchaseInvoice->id,
                            'payment_amount'   => $upiAmount,
                            'payment_date'     => now(),
                            'payment_method'   => 'Online',
                            'payment_type'     => $paidType,
                            'cash_amount'      => 0,
                            'upi_amount'       => $upiAmount,
                            'remaining_amount' => $pending,
                            'status'           => 'debit', // ✅ ADD THIS
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                        ]);
                    }
                }

                // ✅ CASE 2: Cash Only
                elseif ($paymentMode === 'cash') {
                    PaymentStore::create([
                        'user_id'          => $vendor_id,
                        'purchase_id'      => $purchaseInvoice->id,
                        'payment_amount'   => $cashAmount,
                        'payment_date'     => now(),
                        'payment_method'   => 'Cash',
                        'payment_type'     => $paidType,
                        'cash_amount'      => $cashAmount,
                        'upi_amount'       => 0,
                        'remaining_amount' => $pending,
                        'status'           => 'debit', // ✅ ADD THIS
                        'emi_month'        => null,
                        'order_id'         => null,
                        'jobcard_id'       => 0,
                        'isDeleted'        => 0,
                    ]);
                }

                // ✅ CASE 3: Online Only
                elseif ($paymentMode === 'online') {
                    PaymentStore::create([
                        'user_id'          => $vendor_id,
                        'purchase_id'      => $purchaseInvoice->id,
                        'payment_amount'   => $upiAmount,
                        'payment_date'     => now(),
                        'payment_method'   => 'online',
                        'payment_type'     => $paidType,
                        'cash_amount'      => 0,
                        'upi_amount'       => $upiAmount,
                        'remaining_amount' => $pending,
                        'status'           => 'debit', // ✅ ADD THIS
                        'bank_id'          => $request->bank_id,
                        'emi_month'        => null,
                        'order_id'         => null,
                        'jobcard_id'       => 0,
                        'isDeleted'        => 0,
                    ]);
                }

                // ✅ CASE 4: Pending (no immediate payment)
                // elseif ($paymentMode === 'pending') {
                //     PaymentStore::create([
                //         'user_id'          => $vendor_id,
                //         'purchase_id'      => $purchaseInvoice->id,
                //         'payment_amount'   => 0,
                //         'payment_date'     => now(),
                //         'payment_method'   => 'Pending',
                //         'payment_type'     => 'none',
                //         'cash_amount'      => 0,
                //         'upi_amount'       => 0,
                //         'remaining_amount' => $request->grand_total ?? 0,
                //         'emi_month'        => null,
                //         'order_id'         => null,
                //         'jobcard_id'       => 0,
                //         'isDeleted'        => 0,
                //     ]);
                // }
            }
            // ✅ After payment handling and before DB::commit()
            if (! empty($request->payment_mode)) {
                $cashAmount = $cashInput;
                // $upiAmount   = (float) ($request->upi_amount ?? 0);
                $upiAmount = $upiInput;

                if (strtolower($request->payment_mode) === 'cash') {
                    $cashAmount = $cashAmount > 0 ? $cashAmount : $directAmount;
                    $upiAmount  = 0;
                } elseif (strtolower($request->payment_mode) === 'online') {
                    $upiAmount  = $upiAmount > 0 ? $upiAmount : $directAmount;
                    $cashAmount = 0;
                }

                $totalPaid = $cashAmount + $upiAmount;
                $remaining = max(0, $grandTotal - $totalPaid);

                // ✅ Update remaining_amount in purchase invoice
                $purchaseInvoice->update([
                    'remaining_amount' => $remaining,
                ]);
            }

            // ==============================================
            // 🔔 CREATE NOTIFICATIONS (Similar to order_sale)
            // ==============================================
            $vendor = User::find($vendor_id);
            // 1. Notification for the vendor (if vendor exists)
            if ($vendor) {
                $vendorNotificationTitle = 'New Purchase Order Created';
                $vendorNotificationMessage = "Dear {$vendor->name}, a new purchase order #{$invoice_number} has been created for you. Total amount: " . ($request->grand_total ?? 0);

                Notification::create([
                    'user_id'   => $userId,
                    'type'      => 'purchase_order',
                    'title'     => $vendorNotificationTitle,
                    'message'   => $vendorNotificationMessage,
                    'link'      => '/print-purchase/' . $purchaseInvoice->id,
                    'is_read'   => 0,
                    'is_sound'  => 0,
                    'branch_id' => $userBranchId ?? $userId,
                ]);
            }



            DB::commit();

            // Fetch latest payment info for this purchase
            $payment = PaymentStore::where('purchase_id', $purchaseInvoice->id)
                ->orderBy('id', 'desc')
                ->first();

            return response()->json([
                'success'        => true,
                'message'        => 'Purchase records created successfully!',
                'invoice_number' => $invoice_number,
                'purchase_id'    => $purchaseInvoice->id, // ✅ Add this line
                'payment'        => $payment,             // <-- add payment info for frontend
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function purchase_list(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     // dd($user);
    //     $branch_id = $user->id ?? null;

    //     if ($user->role === 'staff' && $user->id) {
    //         $branch_id = $user->id;
    //     } elseif ($user->role === 'sub-admin') {
    //         $branch_id = $user->id;
    //     } elseif ($user->role === 'admin' && ! empty($request->selectedSubAdminId)) {
    //         $branch_id = (int) $request->selectedSubAdminId;
    //     }

    //     // if (!empty($request->selectedSubAdminId)) {
    //     //     $branch_id = $request->selectedSubAdminId;
    //     // }
    //     if ($user->role === 'staff') {
    //         $query = DB::table('purchase_invoice')
    //             ->join('purchases', function ($join) use ($branch_id) {
    //                 $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
    //                     ->where('purchases.isDeleted', '=', 0)
    //                     ->where('purchases.created_by', '=', $branch_id);
    //             });
    //     } else {
    //         $query = DB::table('purchase_invoice')
    //             ->join('purchases', function ($join) use ($branch_id) {
    //                 $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
    //                     ->where('purchases.isDeleted', '=', 0)
    //                     ->where('purchases.branch_id', '=', $branch_id);
    //             });
    //     }

    //     // ✅ Continue common joins + select for both cases
    //     $query = $query
    //         ->join('users', 'purchases.vendor_id', '=', 'users.id')
    //         ->join('products', 'purchases.item', '=', 'products.id')
    //         ->select(
    //             'purchase_invoice.id',
    //             'users.name as vendor_name',
    //             'purchase_invoice.invoice_number',
    //             'purchase_invoice.grand_total',
    //             'purchase_invoice.remaining_amount',
    //             'purchases.purchase_status',
    //             'purchases.payment_status',
    //             'purchase_invoice.created_at as date',
    //             DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as product_names"),
    //             DB::raw("GROUP_CONCAT(purchases.price SEPARATOR ', ') as product_prices"),
    //             DB::raw("GROUP_CONCAT(purchases.quantity SEPARATOR ', ') as product_quantities"),
    //             //     DB::raw("(SELECT COUNT(*) FROM payment_store
    //             //       WHERE payment_store.purchase_id = purchase_invoice.id
    //             //       AND payment_store.isDeleted = 0) as has_payment")
    //             // )
    //             DB::raw("(SELECT COALESCE(SUM(payment_store.payment_amount),0)
    //             FROM payment_store
    //             WHERE payment_store.purchase_id = purchase_invoice.id
    //             AND payment_store.isDeleted = 0) as total_paid"),
    //             DB::raw("(SELECT COALESCE(SUM(purchase_returns.total_amount), 0)
    //             FROM purchase_returns
    //             WHERE purchase_returns.purchase_id = purchase_invoice.id
    //             AND purchase_returns.isDeleted = 0) as total_return")
    //         )
    //         ->where('purchase_invoice.isDeleted', '=', 0);
    //     // ✅ Only non-deleted invoices

    //     if ($request->has('date') && ! empty($request->date)) {
    //         // 🟩 Only apply date filter — ignore month & year
    //         $query->whereDate('purchases.created_at', $request->date);
    //     } else {
    //         // 🟦 If date is not set, apply month & year
    //         if ($request->has('month') && ! empty($request->month)) {
    //             $query->whereMonth('purchases.created_at', $request->month);
    //         }

    //         if ($request->has('year') && ! empty($request->year)) {
    //             $query->whereYear('purchases.created_at', $request->year);
    //         }
    //     }

    //     $purchases = $query
    //         ->groupBy(
    //             'purchase_invoice.id',
    //             'users.name',
    //             'purchase_invoice.invoice_number',
    //             'purchase_invoice.grand_total',
    //             'purchase_invoice.remaining_amount',
    //             'purchases.purchase_status',
    //             'purchases.payment_status',
    //             'purchase_invoice.created_at'
    //         )
    //         ->orderBy('purchase_invoice.id', 'desc')
    //         ->get();

    //     foreach ($purchases as $purchase) {

    //         $grandTotal  = (float) $purchase->grand_total;
    //         $totalPaid   = (float) $purchase->total_paid;
    //         $totalReturn = (float) ($purchase->total_return ?? 0);

    //         // ✅ Remaining Amount (accounts for returns)
    //         $purchase->remaining_amount = max(0, $grandTotal - $totalPaid - $totalReturn);

    //         // ✅ Extra Paid
    //         $purchase->extra_paid = max(0, $totalPaid + $totalReturn - $grandTotal);
    //     }
    //     // After fetching $purchases (as a collection or array)
    //     // foreach ($purchases as $purchase) {
    //     //     $payment = \App\Models\PaymentStore::where('purchase_id', $purchase->id)
    //     //         ->orderBy('id', 'desc')
    //     //         ->first();
    //     //     $purchase->pending_amount = $payment ? $payment->remaining_amount : 0;
    //     // }

    //     // ✅ OPTIMIZED: Cache currency settings
    //     $settings = cache()->remember("settings_branch_{$branch_id}", 300, function () use ($branch_id) {
    //         return DB::table('settings')->where('branch_id', $branch_id)->first();
    //     });
    //     $currencySymbolRaw = $settings->currency_symbol ?? '₹';
    //     $currencySymbol    = trim(html_entity_decode($currencySymbolRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    //     $currencyPosition  = $settings->currency_position ?? 'left';

    //     return response()->json([
    //         'success'           => true,
    //         'currency_symbol'   => $currencySymbol,
    //         'currency_position' => $currencyPosition,
    //         'data'              => $purchases,
    //     ]);
    // }


    public function purchase_list(Request $request)
    {
        $user = Auth::guard('api')->user();
        $branch_id = StaffDepartmentScope::resolveBranchId($user, $request->selectedSubAdminId);

        // Pagination parameters
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $search = $request->input('search', '');

        $query = DB::table('purchase_invoice')
            ->leftJoin('purchases', function ($join) use ($branch_id) {
                $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                    ->where('purchases.isDeleted', '=', 0)
                    ->where('purchases.branch_id', '=', $branch_id);
            })
            ->leftJoin('users', 'purchase_invoice.vendor_id', '=', 'users.id')
            ->leftJoin('products', 'purchases.item', '=', 'products.id')
            ->select(
                'purchase_invoice.id',
                'users.name as vendor_name',
                'users.phone as vendor_phone',
                'purchase_invoice.bill_no',
                'purchase_invoice.invoice_number',
                'purchase_invoice.grand_total',
                'purchase_invoice.remaining_amount',
                DB::raw("MAX(purchases.purchase_status) as purchase_status"),
                DB::raw("MAX(purchases.payment_status) as payment_status"),
                'purchase_invoice.created_at as date',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as product_names"),
                DB::raw("GROUP_CONCAT(purchases.price SEPARATOR ', ') as product_prices"),
                DB::raw("GROUP_CONCAT(purchases.quantity SEPARATOR ', ') as product_quantities"),
                DB::raw("(SELECT COALESCE(SUM(payment_store.payment_amount),0)
                FROM payment_store
                WHERE payment_store.purchase_id = purchase_invoice.id
                AND payment_store.isDeleted = 0) as total_paid"),
                DB::raw("(SELECT COALESCE(SUM(purchase_returns.total_amount), 0)
                FROM purchase_returns
                WHERE purchase_returns.purchase_id = purchase_invoice.id
                AND purchase_returns.isDeleted = 0) as total_return")
            )
            ->where('purchase_invoice.isDeleted', '=', 0)
            ->where('purchase_invoice.branch_id', '=', $branch_id);

        StaffDepartmentScope::applyPurchaseCreatedByScope($query, $user, 'purchase_invoice.created_by');

        // Apply search filter on invoice number or vendor name

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {

                $q->where('purchase_invoice.invoice_number', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_invoice.bill_no', 'LIKE', "%{$search}%")
                    ->orWhere('users.name', 'LIKE', "%{$search}%")
                    ->orWhere('purchases.purchase_status', 'LIKE', "%{$search}%")
                    ->orWhere('purchases.payment_status', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_invoice.grand_total', 'LIKE', "%{$search}%")

                    // Search by DATE
                    ->orWhereDate('purchase_invoice.created_at', $search)

                    // Search formatted date text
                    ->orWhereRaw("DATE_FORMAT(purchase_invoice.created_at,'%d-%b-%Y') LIKE ?", ["%{$search}%"])

                    // Search Return Status
                    ->orWhereRaw("
                (
                    SELECT COALESCE(SUM(purchase_returns.total_amount),0)
                    FROM purchase_returns
                    WHERE purchase_returns.purchase_id = purchase_invoice.id
                    AND purchase_returns.isDeleted = 0
                ) > 0
                AND ? LIKE '%return%'
          ", [$search])

                    // Search Extra Paid keyword
                    ->orWhereRaw("
                (
                    SELECT COALESCE(SUM(payment_store.payment_amount),0)
                    FROM payment_store
                    WHERE payment_store.purchase_id = purchase_invoice.id
                    AND payment_store.isDeleted = 0
                ) > purchase_invoice.grand_total
                AND ? LIKE '%extra%'
          ", [$search]);
            });
        }

        // Apply date filters

        // ✅ CORRECT - use purchase_invoice.created_at (matches displayed date)
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('purchase_invoice.created_at', $request->date);
        } else {
            if ($request->has('month') && !empty($request->month)) {
                $query->whereMonth('purchase_invoice.created_at', $request->month);
            }
            if ($request->has('year') && !empty($request->year)) {
                $query->whereYear('purchase_invoice.created_at', $request->year);
            }
        }

        $this->applyFinancialYearFilter($query, $request->input('financial_year'), 'purchase_invoice.created_at');

        $groupByColumns = [
            'purchase_invoice.id',
            'users.name',
            'users.phone',
            'purchase_invoice.bill_no',
            'purchase_invoice.invoice_number',
            'purchase_invoice.grand_total',
            'purchase_invoice.remaining_amount',
            'purchase_invoice.created_at',
        ];

        // Get total count for pagination
        $totalQuery = clone $query;
        $totalCount = $totalQuery->count(DB::raw('DISTINCT purchase_invoice.id'));
        $lastPage = max(1, (int) ceil($totalCount / $perPage));
        $page = min($page, $lastPage);

        $summaryPurchases = (clone $query)
            ->groupBy(...$groupByColumns)
            ->get();

        $totalAmount = 0;
        $totalPendingAmount = 0;
        $totalPaidAmount = 0;

        foreach ($summaryPurchases as $summaryPurchase) {
            $grandTotal = (float) ($summaryPurchase->grand_total ?? 0);
            $summaryTotalPaid = (float) ($summaryPurchase->total_paid ?? 0);
            $summaryTotalReturn = (float) ($summaryPurchase->total_return ?? 0);
            $remaining = max(0, $grandTotal - $summaryTotalPaid - $summaryTotalReturn);
            $effectivePaid = max(0, min($grandTotal, $grandTotal - $remaining));

            $totalAmount += $grandTotal;
            $totalPendingAmount += $remaining;
            $totalPaidAmount += $effectivePaid;
        }

        // Apply pagination
        $purchases = $query
            ->groupBy(...$groupByColumns)
            ->orderBy('purchase_invoice.id', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        foreach ($purchases as $purchase) {
            $grandTotal  = (float) $purchase->grand_total;
            $totalPaid   = (float) $purchase->total_paid;
            $totalReturn = (float) ($purchase->total_return ?? 0);

            $purchase->remaining_amount = max(0, $grandTotal - $totalPaid - $totalReturn);
            $purchase->extra_paid = max(0, $totalPaid + $totalReturn - $grandTotal);
        }

        // Cache currency settings
        $settings = cache()->remember("settings_branch_{$branch_id}", 300, function () use ($branch_id) {
            return DB::table('settings')->where('branch_id', $branch_id)->first();
        });
        $currencySymbolRaw = $settings->currency_symbol ?? '₹';
        $currencySymbol    = trim(html_entity_decode($currencySymbolRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $currencyPosition  = $settings->currency_position ?? 'left';
   $financialYearEnabled = (bool) ($settings->financial_year ?? true);

        return response()->json([
            'success'           => true,
            'currency_symbol'   => $currencySymbol,
            'currency_position' => $currencyPosition,
            'financial_year_enabled' => $financialYearEnabled,
            'total_amount'      => round($totalAmount, 2),
            'total_pending_amount' => round($totalPendingAmount, 2),
            'total_paid_amount' => round($totalPaidAmount, 2),
            'data'              => $purchases,
            'pagination' => [
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'per_page' => (int)$perPage,
                'total' => $totalCount,
                'from' => $purchases->count() > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $purchases->count() > 0 ? (($page - 1) * $perPage) + $purchases->count() : 0,
            ]
        ]);
    }

    // public function showPurchase($id, Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     // $branch_id = $user->id;
    //     // if (! empty($request->selectedSubAdminId)) {
    //     //     $branch_id = $request->selectedSubAdminId;
    //     // }
    //     // 🧩 Determine branch_id based on role
    //     if ($user->role === 'staff') {
    //         // Staff can only access their own branch
    //         $branch_id = $user->branch_id;
    //     } elseif (! empty($request->selectedSubAdminId)) {
    //         // Admin viewing specific sub-admin’s branch
    //         $branch_id = $request->selectedSubAdminId;
    //     } else {
    //         // Default to user's branch (for admin or superadmin)
    //         $branch_id = $user->branch_id ?? $user->id;
    //     }

    //     try {
    //         $purchase = DB::table('purchases')
    //             ->join('products', 'purchases.item', '=', 'products.id')
    //             ->join('categories', 'products.category_id', '=', 'categories.id')
    //             ->join('users', 'purchases.vendor_id', '=', 'users.id')
    //             ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
    //             ->where('purchases.invoice_id', $id)
    //             ->where('purchases.branch_id', '=', $branch_id)
    //             ->where('purchases.isDeleted', 0)
    //             ->select(
    //                 'purchases.vendor_id',
    //                 'purchases.payment_status',
    //                 'users.phone as vendor_phone',
    //                 'purchase_invoice.shipping',
    //                 'purchase_invoice.status',
    //                 'purchase_invoice.taxes', // ✅ Fetch taxes as JSON
    //                 'purchase_invoice.gst_option',
    //                 'purchase_invoice.grand_total',
    //                 DB::raw("GROUP_CONCAT(purchases.item ORDER BY purchases.id SEPARATOR ', ') as product_ids"),
    //                 DB::raw("GROUP_CONCAT(products.name ORDER BY purchases.id SEPARATOR ', ') as product_names"),
    //                 DB::raw("GROUP_CONCAT(purchases.price ORDER BY purchases.id SEPARATOR ', ') as product_prices"),
    //                 DB::raw("GROUP_CONCAT(purchases.quantity ORDER BY purchases.id SEPARATOR ', ') as product_quantities"),
    //                 DB::raw("GROUP_CONCAT(COALESCE(purchases.discount_percent, 0) ORDER BY purchases.id SEPARATOR ', ') as discount_percents"),
    //                 DB::raw("GROUP_CONCAT(COALESCE(purchases.discount_amount, 0) ORDER BY purchases.id SEPARATOR ', ') as discount_amounts"),
    //                 DB::raw("GROUP_CONCAT(products.category_id ORDER BY purchases.id SEPARATOR ', ') as category_ids"),
    //                 DB::raw("GROUP_CONCAT(categories.name ORDER BY purchases.id SEPARATOR ', ') as category_names"),
    //                 DB::raw("GROUP_CONCAT(products.images ORDER BY purchases.id SEPARATOR ', ') as product_images"),
    //                 DB::raw("GROUP_CONCAT(COALESCE(purchases.product_gst_details, '[]') ORDER BY purchases.id SEPARATOR '|||') as product_gst_details"),
    //                 DB::raw("SUM(purchases.price * purchases.quantity) as total_amount"),
    //                 // DB::raw("(SUM(purchases.price * purchases.quantity) + purchase_invoice.shipping) as grand_total")
    //             )
    //             ->groupBy(
    //                 'purchases.vendor_id',
    //                 'purchases.payment_status',
    //                 'users.phone',
    //                 'purchase_invoice.shipping',
    //                 'purchase_invoice.status',
    //                 'purchase_invoice.taxes', // ✅ Include taxes in grouping
    //                 'purchase_invoice.gst_option',
    //                 'purchase_invoice.grand_total',
    //             )
    //             ->first();

    //         $invoice = PurchaseInvoice::where('id', $id)
    //             ->where('branch_id', $branch_id)
    //             ->where('isDeleted', 0)
    //             ->first();

    //         $vendor = User::find($invoice->vendor_id);

    //         $productImages = explode(',', $purchase->product_images ?? '');
    //         $basePath      = env('ImagePath', '/');

    //         $productImageUrls = [];

    //         if (! empty($productImages)) {
    //             $productImageUrls = array_map(function ($img) use ($basePath) {
    //                 // Remove extra brackets, quotes, or spaces
    //                 $img = trim($img, " []\"'");
    //                 return url(rtrim($basePath, '/') . '/storage/' . ltrim($img, '/'));
    //             }, $productImages);

    //             // Remove any empty values
    //             $productImageUrls = array_filter($productImageUrls);
    //         }

    //         if (empty($productImageUrls)) {
    //             $productImageUrls = [url(rtrim($basePath, '/') . '/admin/assets/img/product/noimage.png')];
    //         }

    //         // dd($productImageUrls);
    //         if (! $purchase) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Purchase not found',
    //             ], 404);
    //         }

    //         // ✅ Decode taxes from JSON format
    //         $taxes = json_decode($purchase->taxes, true) ?? [];

    //         // ✅ OPTIMIZED: Cache settings
    //         $settings = cache()->remember("setting_branch_{$branch_id}", 300, function () use ($branch_id) {
    //             return DB::table('settings')->where('branch_id', $branch_id)->first();
    //         });

    //         return response()->json([
    //             'success'            => true,
    //             'data'               => $purchase,
    //             'taxes'              => $taxes, // ✅ Send taxes to the frontend
    //             'companyInfo'        => $settings,
    //             'invoice'            => $invoice,
    //             'vendor'             => $vendor,
    //             'product_image_urls' => array_values($productImageUrls),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function showPurchase($id, Request $request)
    {
        $user = Auth::guard('api')->user();

        // 🧩 Determine branch_id based on role
        if ($user->role === 'staff') {
            $branch_id = $user->branch_id;
        } elseif (! empty($request->selectedSubAdminId)) {
            $branch_id = $request->selectedSubAdminId;
        } else {
            $branch_id = $user->branch_id ?? $user->id;
        }

        try {
            $purchase = DB::table('purchases')
                ->join('products', 'purchases.item', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('users', 'purchases.vendor_id', '=', 'users.id')
                ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
                ->where('purchases.invoice_id', $id)
                ->where('purchases.branch_id', '=', $branch_id)
                ->where('purchases.isDeleted', 0)
                ->select(
                    'purchases.vendor_id',
                    'purchases.payment_status',
                    'users.phone as vendor_phone',
                    'purchase_invoice.shipping',
                    'purchase_invoice.status',
                    'purchase_invoice.taxes',
                    'purchase_invoice.gst_option',
                    'purchase_invoice.grand_total',
                    'purchase_invoice.remaining_amount',
                    DB::raw("GROUP_CONCAT(purchases.item ORDER BY purchases.id SEPARATOR ', ') as product_ids"),
                    DB::raw("GROUP_CONCAT(products.name ORDER BY purchases.id SEPARATOR ', ') as product_names"),
                    DB::raw("GROUP_CONCAT(purchases.price ORDER BY purchases.id SEPARATOR ', ') as product_prices"),
                    DB::raw("GROUP_CONCAT(purchases.quantity ORDER BY purchases.id SEPARATOR ', ') as product_quantities"),
                    DB::raw("GROUP_CONCAT(COALESCE(purchases.discount_percent, 0) ORDER BY purchases.id SEPARATOR ', ') as discount_percents"),
                    DB::raw("GROUP_CONCAT(COALESCE(purchases.discount_amount, 0) ORDER BY purchases.id SEPARATOR ', ') as discount_amounts"),
                    DB::raw("GROUP_CONCAT(products.category_id ORDER BY purchases.id SEPARATOR ', ') as category_ids"),
                    DB::raw("GROUP_CONCAT(categories.name ORDER BY purchases.id SEPARATOR ', ') as category_names"),
                    DB::raw("GROUP_CONCAT(products.images ORDER BY purchases.id SEPARATOR ', ') as product_images"),
                    DB::raw("GROUP_CONCAT(COALESCE(purchases.product_gst_details, '[]') ORDER BY purchases.id SEPARATOR '|||') as product_gst_details"),
                    DB::raw("GROUP_CONCAT(COALESCE(purchases.imei_no, '[]') ORDER BY purchases.id SEPARATOR '|||') as product_imei_nos"),
                    DB::raw("SUM(purchases.price * purchases.quantity) as total_amount")
                )
                ->groupBy(
                    'purchases.vendor_id',
                    'purchases.payment_status',
                    'users.phone',
                    'purchase_invoice.shipping',
                    'purchase_invoice.status',
                    'purchase_invoice.taxes',
                    'purchase_invoice.gst_option',
                    'purchase_invoice.grand_total',
                    'purchase_invoice.remaining_amount'
                )
                ->first();

            $invoice = PurchaseInvoice::where('id', $id)
                ->where('branch_id', $branch_id)
                ->where('isDeleted', 0)
                ->first();

            if (! $purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found',
                ], 404);
            }

            // ✅ Fetch payment details from payment_store table
            $paymentDetails = DB::table('payment_store')
                ->where('purchase_id', $id)
                ->where('isDeleted', 0)
                ->orderBy('id', 'desc')
                ->first();

            // ✅ Set payment details from payment_store
            if ($paymentDetails) {
                // Determine payment mode
                if ($paymentDetails->payment_method == 'Pending' || empty($paymentDetails->payment_method)) {
                    $purchase->payment_mode = 'pending';
                } elseif ($paymentDetails->payment_method == 'Cash') {
                    $purchase->payment_mode = 'cash';
                } elseif ($paymentDetails->payment_method == 'Online') {
                    $purchase->payment_mode = 'online';
                } elseif ($paymentDetails->cash_amount > 0 && $paymentDetails->upi_amount > 0) {
                    $purchase->payment_mode = 'cashonline';
                } elseif ($paymentDetails->cash_amount > 0) {
                    $purchase->payment_mode = 'cash';
                } elseif ($paymentDetails->upi_amount > 0) {
                    $purchase->payment_mode = 'online';
                } else {
                    $purchase->payment_mode = 'pending';
                }

                $purchase->paid_type = $paymentDetails->payment_type;
                $purchase->bank_id = $paymentDetails->bank_id;
                $purchase->cash_amount = $paymentDetails->cash_amount;
                $purchase->upi_amount = $paymentDetails->upi_amount;
                $purchase->amount = $paymentDetails->payment_amount;
            } else {
                // Default values if no payment found
                $purchase->payment_mode = 'pending';
                $purchase->paid_type = null;
                $purchase->bank_id = null;
                $purchase->cash_amount = 0;
                $purchase->upi_amount = 0;
                $purchase->amount = 0;
            }

            // ✅ Calculate total paid amount
            $totalPaid = DB::table('payment_store')
                ->where('purchase_id', $id)
                ->where('isDeleted', 0)
                ->sum('payment_amount');

            $purchase->total_paid = $totalPaid;
            $purchase->remaining_amount = max(0, ($purchase->grand_total ?? 0) - $totalPaid);

            $vendor = User::find($invoice->vendor_id ?? $purchase->vendor_id ?? null);

            $productImages = explode(',', $purchase->product_images ?? '');
            $basePath      = env('ImagePath', '/');

            $productImageUrls = [];

            if (! empty($productImages)) {
                $productImageUrls = array_map(function ($img) use ($basePath) {
                    $img = trim($img, " []\"'");
                    return url(rtrim($basePath, '/') . '/storage/' . ltrim($img, '/'));
                }, $productImages);
                $productImageUrls = array_filter($productImageUrls);
            }

            if (empty($productImageUrls)) {
                $productImageUrls = [url(rtrim($basePath, '/') . '/admin/assets/img/product/noimage.png')];
            }

            // ✅ Decode taxes from JSON format
            $taxes = json_decode($purchase->taxes, true) ?? [];

            // ✅ OPTIMIZED: Cache settings
            $settings = cache()->remember("setting_branch_{$branch_id}", 300, function () use ($branch_id) {
                return DB::table('settings')->where('branch_id', $branch_id)->first();
            });

            // ✅ Get bank details if bank_id exists
            $bankDetails = null;
            if (!empty($purchase->bank_id)) {
                $bankDetails = DB::table('bank_master')
                    ->where('id', $purchase->bank_id)
                    ->where('isDeleted', 0)
                    ->first();
            }


            return response()->json([
                'success'            => true,
                'data'               => $purchase,
                'taxes'              => $taxes,
                'companyInfo'        => $settings,
                'invoice'            => $invoice,
                'vendor'             => $vendor,
                'product_image_urls' => array_values($productImageUrls),
                'bank_details'       => $bankDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function purchase_invoice_pdf_download($id)
    {
        $authUser   = Auth::guard('api')->user();
        $subAdminId = session('selectedSubAdminId') ?? $authUser->id;

        // ✅ OPTIMIZED: Cache settings and eager load invoice with vendor
        $setting = cache()->remember("setting_branch_{$subAdminId}", 300, function () use ($subAdminId) {
            return Setting::where('branch_id', $subAdminId)->first();
        });

        $invoice = PurchaseInvoice::with('vendor')->find($id);

        if (! $invoice) {
            return response()->json([
                'status'  => false,
                'message' => 'Purchase invoice not found.',
            ], 404);
        }

        // ✅ OPTIMIZED: Use already loaded vendor relationship
        $vendor = $invoice->vendor ?? (object) [
            'name'    => 'Unknown Vendor',
            'email'   => '',
            'phone'   => '',
            'address' => '',
        ];

        // ✅ OPTIMIZED: Eager load purchase items with product
        $purchaseItems = Purchases::with('product')
            ->where('invoice_id', $invoice->id)
            ->get();
        $paymentStatus = $purchaseItems->first()->payment_status ?? 'Pending';
        $paymentMethod = $purchaseItems->first()->payment_method ?? 'N/A';

        // Subtotal
        $subtotal = $purchaseItems->sum(fn($item) => $item->price * $item->quantity);

        // Discount
        $discountPercent = $invoice->discount ?? 0;
        $discountAmount  = ($discountPercent / 100) * $subtotal;
        $afterDiscount   = $subtotal - $discountAmount;

        // Shipping
        $shipping = $invoice->shipping ?? 0;

        // Taxes
        $taxRates   = json_decode($invoice->taxes, true) ?? [];
        $taxDetails = [];
        foreach ($taxRates as $tax) {
            $taxAmount    = ($tax['rate'] / 100) * $afterDiscount;
            $taxDetails[] = [
                'name'   => $tax['name'],
                'rate'   => $tax['rate'],
                'amount' => $taxAmount,
            ];
        }

        // Grand Total
        $grandTotal = $afterDiscount + $shipping + collect($taxDetails)->sum('amount');

        // Format currency
        $formatCurrency = fn($amt) => $setting->currency_position === 'right'
            ? number_format($amt, 2) . $setting->currency_symbol
            : $setting->currency_symbol . number_format($amt, 2);

        // Prepare data for PDF
        $pdfData = [
            'invoice'        => $invoice,
            'vendor'         => $vendor,
            'purchaseItems'  => $purchaseItems,
            'setting'        => $setting,
            'subtotal'       => $formatCurrency($subtotal),
            'discount'       => $invoice->discount,
            'discountAmount' => $formatCurrency($discountAmount),
            'afterDiscount'  => $formatCurrency($afterDiscount),
            'shipping'       => $formatCurrency($shipping),
            'taxDetails'     => $taxDetails,
            'grandTotal'     => $formatCurrency($grandTotal),
            'payment_status' => ucfirst($paymentStatus),
            'payment_method' => ucfirst($paymentMethod),
        ];

        // Generate PDF
        $pdf = PDF::loadView('purchase.purchase-invoice-pdf', $pdfData);

        // Save PDF to storage
        $fileName = 'purchase_invoice_' . $id . '.pdf';
        // $filePath = 'public/storage/purchase-invoices/' . $fileName;

        // if (! file_exists(storage_path('app/public/purchase-invoices'))) {
        //     mkdir(storage_path('app/public/purchase-invoices'), 0777, true);
        // }
        $relativePath = 'purchase-invoices/' . $fileName;

        Storage::disk('public')->put($relativePath, $pdf->output());

        // Generate public URL

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        // Return JSON response
        return response()->json([
            'status'    => true,
            'message'   => 'Purchase Invoice PDF generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    // public function purchase_update(Request $request, $invoice_id)
    // {
    //     $user         = Auth::guard('api')->user();
    //     $userId       = $user->id;
    //     $userBranchId = $user->branch_id;
    //     $role         = $user->role;

    //     if ($role === 'staff' && $user->branch_id) {
    //         $userBranchId = $user->branch_id;
    //     } elseif ($role === 'admin' && ! empty($request->selectedSubAdminId)) {
    //         $userBranchId = $request->selectedSubAdminId;
    //     } else {
    //         $userBranchId = $user->id;
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'vendor_id'              => 'required',
    //         'status'                 => 'required|in:pending,partially,completed',
    //         'payment_status'         => 'required|in:pending,partially,completed',
    //         'bill_no'                => 'required|unique:purchase_invoice,bill_no,' . $invoice_id . ',id',
    //         'purchase_date'          => 'nullable|date',
    //         'remark'                => 'nullable|string',
    //         'shipping'               => 'required|numeric',
    //         'grand_total'            => 'required|numeric',
    //         'gst_option'             => 'required',
    //         'discount'               => 'nullable|numeric',
    //         'taxes'                  => 'nullable|array',
    //         'products'               => 'required|array|min:1',
    //         'products.*.id'          => 'required',
    //         'products.*.category_id' => 'required',
    //         'products.*.price'       => 'required|numeric|min:0',
    //         'products.*.quantity'    => 'required|numeric|min:1',
    //         'products.*.total'       => 'required|numeric|min:0',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors'  => $validator->errors(),
    //         ], 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $purchaseInvoice = PurchaseInvoice::find($invoice_id);
    //         if (! $purchaseInvoice) {
    //             return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
    //         }

    //         $purchaseDate = $request->filled('purchase_date')
    //             ? Carbon::parse($request->purchase_date . ' ' . ($purchaseInvoice->created_at
    //                 ? $purchaseInvoice->created_at->format('H:i:s')
    //                 : now()->format('H:i:s')))
    //             : $purchaseInvoice->created_at;

    //         /* ===============================
    //        1️⃣ GET OLD PURCHASE DATA
    //     =============================== */
    //         $oldPurchases   = Purchases::where('invoice_id', $invoice_id)->get();
    //         $oldPurchaseMap = $oldPurchases->keyBy('item');

    //         $newProductIds     = [];
    //         $processedProducts = [];

    //         // ✅ Bulk load products for GST calculations
    //         $productIds = array_filter(array_column($request->products, 'id'), 'is_numeric');
    //         $existingProducts = ! empty($productIds)
    //             ? Product::whereIn('id', $productIds)->get()->keyBy('id')
    //             : collect();

    //         /* ===============================
    //        2️⃣ LOOP NEW PRODUCTS
    //     =============================== */
    //         foreach ($request->products as $product) {

    //             // Category
    //             $category_id = is_numeric($product['category_id'])
    //                 ? $product['category_id']
    //                 : Category::create(['name' => $product['category_id']])->id;

    //             // Product
    //             if (! is_numeric($product['id'])) {
    //                 $sku        = mt_rand(10000000, 99999999);
    //                 $newProduct = Product::create([
    //                     'name'        => $product['id'],
    //                     'category_id' => $category_id,
    //                     'price'       => $product['price'],
    //                     'quantity'    => $product['quantity'],
    //                     'vendor_id'   => $request->vendor_id,
    //                     'status'      => 'active',
    //                     'SKU'         => $sku,
    //                 ]);
    //                 $product_id = $newProduct->id;
    //                 $oldQty     = 0;
    //             } else {
    //                 $product_id = $product['id'];
    //                 $oldQty     = $oldPurchaseMap[$product_id]->quantity ?? 0;

    //                 $productModel = Product::find($product_id);
    //                 $qtyDiff      = $product['quantity'] - $oldQty;

    //                 if ($qtyDiff > 0) {
    //                     $productModel->increment('quantity', $qtyDiff);
    //                 } elseif ($qtyDiff < 0) {
    //                     $productModel->decrement('quantity', abs($qtyDiff));
    //                 }

    //                 ProductInventory::create([
    //                     'product_id'    => $product_id,
    //                     'initial_stock' => $productModel->quantity - $qtyDiff,
    //                     'current_stock' => $productModel->quantity,
    //                     'branch_id'     => $purchaseInvoice->branch_id,
    //                     'create_by'     => auth()->id(),
    //                     'type'          => 'Purchase Update',
    //                     'date'          => now(),
    //                 ]);
    //             }

    //             $newProductIds[] = $product_id;

    //             // Product-wise GST calculation
    //             $gst_details = [];
    //             $gst_total   = 0;
    //             $baseTotal   = $product['price'] * $product['quantity'];

    //             if ($request->gst_option === 'with') {
    //                 $prod = $existingProducts->get($product_id);
    //                 if ($prod && $prod->gst_option === 'with_gst' && $prod->product_gst) {
    //                     $taxes = json_decode($prod->product_gst, true);
    //                     if (is_array($taxes)) {
    //                         foreach ($taxes as $tax) {
    //                             $taxRate   = floatval($tax['tax_rate'] ?? 0);
    //                             $taxAmount = ($baseTotal * $taxRate) / 100;
    //                             $gst_total += $taxAmount;
    //                             $gst_details[] = [
    //                                 'name'   => $tax['tax_name'] ?? '',
    //                                 'rate'   => $taxRate,
    //                                 'amount' => $taxAmount,
    //                             ];
    //                         }
    //                     }
    //                 }
    //             }

    //             Purchases::updateOrCreate(
    //                 ['invoice_id' => $invoice_id, 'item' => $product_id],
    //                 [
    //                     'quantity'            => $product['quantity'],
    //                     'price'               => $product['price'],
    //                     'discount_percent'    => $product['discount_percent'] ?? 0,
    //                     'discount_amount'     => $product['discount_amount'] ?? 0,
    //                     'amount_total'        => $product['total'],
    //                     'product_gst_details' => $gst_details,
    //                     'product_gst_total'   => $gst_total,
    //                     'vendor_id'           => $request->vendor_id,
    //                     'branch_id'           => $userBranchId,
    //                     'created_by'          => $userId,
    //                 ]
    //             );

    //             $processedProducts[] = [
    //                 'product_id'       => $product_id,
    //                 'price'            => $product['price'],
    //                 'quantity'         => $product['quantity'],
    //                 'discount_percent' => $product['discount_percent'] ?? 0,
    //                 'discount_amount'  => $product['discount_amount'] ?? 0,
    //                 'total'            => $product['total'],
    //             ];
    //         }

    //         /* ===============================
    //        3️⃣ DELETE REMOVED PRODUCTS
    //     =============================== */
    //         $deletedProducts = $oldPurchases->whereNotIn('item', $newProductIds);

    //         foreach ($deletedProducts as $deleted) {
    //             $product = Product::find($deleted->item);
    //             if ($product) {
    //                 $product->decrement('quantity', $deleted->quantity);

    //                 ProductInventory::create([
    //                     'product_id'    => $product->id,
    //                     'initial_stock' => $product->quantity + $deleted->quantity,
    //                     'current_stock' => $product->quantity,
    //                     'branch_id'     => $purchaseInvoice->branch_id,
    //                     'create_by'     => auth()->id(),
    //                     'type'          => 'Purchase Item Removed',
    //                     'date'          => now(),
    //                 ]);
    //             }
    //         }

    //         Purchases::where('invoice_id', $invoice_id)
    //             ->whereNotIn('item', $newProductIds)
    //             ->delete();

    //         /* ===============================
    //        4️⃣ TOTAL / PAYMENT LOGIC
    //     =============================== */
    //         $totalAmount = collect($processedProducts)->sum('total');
    //         $shipping    = $request->shipping;

    //         $totalPaid = PaymentStore::where('purchase_id', $invoice_id)
    //             ->where('isDeleted', 0)
    //             ->sum('payment_amount');

    //         $grandTotal = $request->grand_total;
    //         $remaining  = max(0, $grandTotal - $totalPaid);

    //         if ($remaining <= 0 && $totalPaid > 0) {
    //             $defaultPurchaseStatus = 'completed';
    //             $defaultPaymentStatus  = 'completed';
    //         } elseif ($totalPaid > 0) {
    //             $defaultPurchaseStatus = 'partially';
    //             $defaultPaymentStatus  = 'partially';
    //         } else {
    //             $defaultPurchaseStatus = 'pending';
    //             $defaultPaymentStatus  = 'pending';
    //         }

    //         $purchaseStatus = $request->filled('status') ? $request->status : $defaultPurchaseStatus;
    //         $paymentStatus  = $request->filled('payment_status') ? $request->payment_status : $defaultPaymentStatus;

    //         /* ===============================
    //        5️⃣ UPDATE INVOICE & ITEMS
    //     =============================== */
    //         $purchaseInvoice->update([
    //             'products'         => json_encode($processedProducts),
    //             'total_amount'     => $totalAmount,
    //             'discount'         => $request->discount ?? 0,
    //             'shipping'         => $shipping,
    //             'grand_total'      => $grandTotal,
    //             'remaining_amount' => $remaining,
    //             'gst_option'       => $request->gst_option === 'with' ? 'with_gst' : 'without_gst',
    //             'taxes'            => json_encode($request->taxes),
    //             'status'           => $purchaseStatus,
    //             'bill_no'          => $request->bill_no,
    //             'purchase_date'   => $purchaseDate,
    //             'remark'          => $request->remark,
    //             'created_at'       => $purchaseDate,
    //         ]);

    //         Purchases::where('invoice_id', $invoice_id)->update([
    //             'purchase_status' => $purchaseStatus,
    //             'payment_status'  => $paymentStatus,
    //             'updated_at'      => now(),
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Purchase updated successfully',
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function purchase_update(Request $request, $invoice_id)
    {
        $user         = Auth::guard('api')->user();
        $userId       = $user->id;
        $userBranchId = $user->branch_id;
        $role         = $user->role;

        if ($role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif ($role === 'admin' && ! empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $validator = Validator::make($request->all(), [
            'vendor_id'              => 'required',
            'status'                 => 'nullable|in:pending,partially,completed',
            'payment_status'         => 'nullable|in:pending,partially,completed',
            'bill_no' => [
                'required',
                Rule::unique('purchase_invoice', 'bill_no')
                    ->ignore($invoice_id)
                    ->where('branch_id', $userBranchId)
                    ->where('isDeleted', 0),
            ],
            'purchase_date'          => 'nullable|date',
            'remark'                 => 'nullable|string',
            'shipping'               => 'required|numeric',
            'grand_total'            => 'required|numeric',
            'gst_option'             => 'required',
            'discount'               => 'nullable|numeric',
            'taxes'                  => 'nullable|array',
            'products'               => 'required|array|min:1',
            'products.*.id'          => 'required',
            'products.*.category_id' => 'required',
            'products.*.price'       => 'required|numeric|min:0',
            'products.*.quantity'    => 'required|numeric|min:1',
            'products.*.total'       => 'required|numeric|min:0',
            'payment_mode'           => 'nullable|string',
            'paid_type'              => 'nullable|string|in:full,partial',
            'bank_id'                => 'required_if:payment_mode,online,cashonline|nullable|exists:bank_master,id',
        ]);

        $validator->after(function ($validator) use ($request, $invoice_id) {
            $allImeis = []; // Check duplicates within the same form
            foreach ($request->input('products', []) as $index => $product) {
                $imeiNoJson = $product['imei_no'] ?? null;
                if (!empty($imeiNoJson)) {
                    $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                    if (is_array($imeiArray)) {
                        foreach ($imeiArray as $imeiIndex => $imei) {
                            if (!empty($imei)) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add(
                                        "imei_error_{$index}_{$imeiIndex}",
                                        "IMEI {$imei} is duplicated in this form."
                                    );
                                }
                                $allImeis[] = $imei;
                                
                                $exists = \DB::table('purchases')
                                    ->where('invoice_id', '!=', $invoice_id)
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($exists) {
                                    $validator->errors()->add(
                                        "imei_error_{$index}_{$imeiIndex}",
                                        "IMEI {$imei} already exists in the system."
                                    );
                                }
                            }
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $purchaseInvoice = PurchaseInvoice::find($invoice_id);
            if (! $purchaseInvoice) {
                return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
            }

            $purchaseDate = $request->filled('purchase_date')
                ? Carbon::parse($request->purchase_date . ' ' . ($purchaseInvoice->created_at
                    ? $purchaseInvoice->created_at->format('H:i:s')
                    : now()->format('H:i:s')))
                : $purchaseInvoice->created_at;

            // Helper function for money normalization
            $normalizeMoney = function($value) {
                return floatval(str_replace(',', '', $value));
            };

            $shippingAmount = $normalizeMoney($request->shipping ?? 0);
            $grandTotal     = $normalizeMoney($request->grand_total ?? 0);
            $cashInput      = $normalizeMoney($request->cash_amount ?? 0);
            $upiInput       = $normalizeMoney($request->upi_amount ?? 0);
            $directAmount   = $normalizeMoney($request->amount ?? 0);

            /* ===============================
        1️⃣ GET OLD PURCHASE DATA
        =============================== */
            $oldPurchases   = Purchases::where('invoice_id', $invoice_id)->get();
            $oldPurchaseMap = $oldPurchases->keyBy('item');

            $newProductIds     = [];
            $processedProducts = [];

            // Bulk load products for GST calculations
            $productIds = array_filter(array_column($request->products, 'id'), 'is_numeric');
            $existingProducts = ! empty($productIds)
                ? Product::whereIn('id', $productIds)->get()->keyBy('id')
                : collect();

            /* ===============================
        2️⃣ LOOP NEW PRODUCTS
        =============================== */
            foreach ($request->products as $product) {
                // Category
                $category_id = is_numeric($product['category_id'])
                    ? $product['category_id']
                    : Category::create(['name' => $product['category_id']])->id;

                // Product
                if (! is_numeric($product['id'])) {
                    do {
                        $sku = mt_rand(10000000, 99999999);
                    } while (Product::where('SKU', $sku)->exists());

                    $newProduct = Product::create([
                        'name'          => $product['id'],
                        'category_id'   => $category_id,
                        'price'         => $product['price'],
                        'quantity'      => $product['quantity'],
                        'vendor_id'     => $request->vendor_id,
                        'availablility' => 'in_stock',
                        'status'        => 'active',
                        'SKU'           => $sku,
                        'branch_id'     => $userBranchId,
                    ]);
                    $product_id = $newProduct->id;
                    $oldQty     = 0;

                    // Add to inventory for new product
                    ProductInventory::create([
                        'product_id'    => $product_id,
                        'initial_stock' => 0,
                        'current_stock' => $product['quantity'],
                        'branch_id'     => $userBranchId,
                        'create_by'     => $userId,
                        'type'          => 'Purchase Added',
                        'date'          => now(),
                    ]);
                } else {
                    $product_id = $product['id'];
                    $oldQty     = $oldPurchaseMap[$product_id]->quantity ?? 0;

                    $productModel = Product::find($product_id);
                    if ($productModel) {
                        $qtyDiff = $product['quantity'] - $oldQty;

                        if ($qtyDiff > 0) {
                            $productModel->increment('quantity', $qtyDiff);
                        } elseif ($qtyDiff < 0) {
                            $productModel->decrement('quantity', abs($qtyDiff));
                        }

                        ProductInventory::create([
                            'product_id'    => $product_id,
                            'initial_stock' => $productModel->quantity - $qtyDiff,
                            'current_stock' => $productModel->quantity,
                            'branch_id'     => $purchaseInvoice->branch_id,
                            'create_by'     => $userId,
                            'type'          => 'Purchase Update',
                            'date'          => now(),
                        ]);
                    }
                }

                $newProductIds[] = $product_id;

                // Product-wise GST calculation
                $gst_details = [];
                $gst_total   = 0;
                $baseTotal   = $product['price'] * $product['quantity'];
                $discount_amount = $product['discount_amount'] ?? 0;
                $taxableAmount   = $baseTotal;

                if ($request->gst_option === 'with') {
                    $prod = $existingProducts->get($product_id);
                    if ($prod && $prod->gst_option === 'with_gst' && $prod->product_gst) {
                        $taxes = json_decode($prod->product_gst, true);
                        if (is_array($taxes)) {
                            foreach ($taxes as $tax) {
                                $taxRate   = floatval($tax['tax_rate'] ?? 0);
                                $taxAmount = ($taxableAmount * $taxRate) / 100;
                                $gst_total += $taxAmount;
                                $gst_details[] = [
                                    'name'   => $tax['tax_name'] ?? '',
                                    'rate'   => $taxRate,
                                    'amount' => $taxAmount,
                                ];
                            }
                        }
                    }
                }

                Purchases::updateOrCreate(
                    ['invoice_id' => $invoice_id, 'item' => $product_id],
                    [
                        'quantity'            => $product['quantity'],
                        'price'               => $product['price'],
                        'discount_percent'    => $product['discount_percent'] ?? 0,
                        'discount_amount'     => $product['discount_amount'] ?? 0,
                        'amount_total'        => $product['total'],
                        'product_gst_details' => $gst_details,
                        'product_gst_total'   => $gst_total,
                        'imei_no'             => $product['imei_no'] ?? null,
                        'vendor_id'           => $request->vendor_id,
                        'branch_id'           => $userBranchId,
                        'created_by'          => $userId,
                    ]
                );

                $processedProducts[] = [
                    'product_id'          => $product_id,
                    'price'               => $product['price'],
                    'quantity'            => $product['quantity'],
                    'discount_percent'    => $product['discount_percent'] ?? 0,
                    'discount_amount'     => $product['discount_amount'] ?? 0,
                    'total'               => $product['total'],
                    'imei_no'             => $product['imei_no'] ?? null,
                    'product_gst_total'   => $gst_total,
                    'product_gst_details' => $gst_details,
                ];
            }

            /* ===============================
        3️⃣ DELETE REMOVED PRODUCTS
        =============================== */
            $deletedProducts = $oldPurchases->whereNotIn('item', $newProductIds);

            foreach ($deletedProducts as $deleted) {
                $product = Product::find($deleted->item);
                if ($product) {
                    $product->decrement('quantity', $deleted->quantity);

                    ProductInventory::create([
                        'product_id'    => $product->id,
                        'initial_stock' => $product->quantity + $deleted->quantity,
                        'current_stock' => $product->quantity,
                        'branch_id'     => $purchaseInvoice->branch_id,
                        'create_by'     => $userId,
                        'type'          => 'Purchase Item Removed',
                        'date'          => now(),
                    ]);
                }
            }

            Purchases::where('invoice_id', $invoice_id)
                ->whereNotIn('item', $newProductIds)
                ->delete();

            /* ===============================
        4️⃣ PAYMENT HANDLING LOGIC
        =============================== */

            // Get existing payment records
            $existingPayments = PaymentStore::where('purchase_id', $invoice_id)
                ->where('isDeleted', 0)
                ->get();

            // Calculate total paid from existing payments
            $totalPaid = $existingPayments->sum('payment_amount');

            // Determine new payment amount based on request
            $paymentMode = $request->payment_mode;
            $paidType = $request->paid_type ?? 'full';

            // Calculate new payment amount
            $newPaymentAmount = 0;
            if (!empty($paymentMode) && $paymentMode !== 'pending') {
                if ($paidType === 'full') {
                    $newPaymentAmount = $grandTotal;
                } else {
                    if ($paymentMode === 'cash' || $paymentMode === 'online') {
                        $newPaymentAmount = $directAmount > 0 ? $directAmount : ($cashInput + $upiInput);
                    } else {
                        $newPaymentAmount = $cashInput + $upiInput;
                    }
                }
            }

            // Calculate remaining amount
            $remainingAmount = max(0, $grandTotal - $newPaymentAmount);

            // Delete old payments if payment mode or amount changed significantly
            // We'll update instead of delete to maintain history
            if (!empty($paymentMode) && $paymentMode !== 'pending') {
                // Delete old payments for this purchase (optional - or keep history)
                // PaymentStore::where('purchase_id', $invoice_id)->where('isDeleted', 0)->delete();

                // Create new payment records based on mode
                if ($paymentMode === 'cash') {
                    $cashAmount = $cashInput > 0 ? $cashInput : $directAmount;
                    if ($cashAmount > 0) {
                        PaymentStore::create([
                            'user_id'          => $request->vendor_id,
                            'purchase_id'      => $invoice_id,
                            'payment_amount'   => $cashAmount,
                            'payment_date'     => now(),
                            'payment_method'   => 'Cash',
                            'payment_type'     => $paidType,
                            'cash_amount'      => $cashAmount,
                            'upi_amount'       => 0,
                            'remaining_amount' => $remainingAmount,
                            'status'           => 'debit',
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                            'branch_id'        => $userBranchId,
                        ]);
                    }
                } elseif ($paymentMode === 'online') {
                    $upiAmount = $upiInput > 0 ? $upiInput : $directAmount;
                    if ($upiAmount > 0) {
                        PaymentStore::create([
                            'user_id'          => $request->vendor_id,
                            'purchase_id'      => $invoice_id,
                            'payment_amount'   => $upiAmount,
                            'payment_date'     => now(),
                            'payment_method'   => 'Online',
                            'payment_type'     => $paidType,
                            'cash_amount'      => 0,
                            'upi_amount'       => $upiAmount,
                            'remaining_amount' => $remainingAmount,
                            'status'           => 'debit',
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                            'branch_id'        => $userBranchId,
                        ]);
                    }
                } elseif ($paymentMode === 'cashonline') {
                    if ($cashInput > 0) {
                        PaymentStore::create([
                            'user_id'          => $request->vendor_id,
                            'purchase_id'      => $invoice_id,
                            'payment_amount'   => $cashInput,
                            'payment_date'     => now(),
                            'payment_method'   => 'Cash',
                            'payment_type'     => $paidType,
                            'cash_amount'      => $cashInput,
                            'upi_amount'       => 0,
                            'remaining_amount' => $remainingAmount,
                            'status'           => 'debit',
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                            'branch_id'        => $userBranchId,
                        ]);
                    }
                    if ($upiInput > 0) {
                        PaymentStore::create([
                            'user_id'          => $request->vendor_id,
                            'purchase_id'      => $invoice_id,
                            'payment_amount'   => $upiInput,
                            'payment_date'     => now(),
                            'payment_method'   => 'Online',
                            'payment_type'     => $paidType,
                            'cash_amount'      => 0,
                            'upi_amount'       => $upiInput,
                            'remaining_amount' => $remainingAmount,
                            'status'           => 'debit',
                            'bank_id'          => $request->bank_id,
                            'emi_month'        => null,
                            'order_id'         => null,
                            'jobcard_id'       => 0,
                            'isDeleted'        => 0,
                            'branch_id'        => $userBranchId,
                        ]);
                    }
                }
            }

            // Recalculate total paid after new payments
            $totalPaidAfterUpdate = PaymentStore::where('purchase_id', $invoice_id)
                ->where('isDeleted', 0)
                ->sum('payment_amount');

            $remainingAfterUpdate = max(0, $grandTotal - $totalPaidAfterUpdate);

            /* ===============================
        5️⃣ DETERMINE STATUSES
        =============================== */
            $totalAmount = collect($processedProducts)->sum('total');

            if ($remainingAfterUpdate <= 0 && $totalPaidAfterUpdate > 0) {
                $defaultPurchaseStatus = 'completed';
                $defaultPaymentStatus = 'completed';
            } elseif ($totalPaidAfterUpdate > 0) {
                $defaultPurchaseStatus = 'partially';
                $defaultPaymentStatus = 'partially';
            } else {
                $defaultPurchaseStatus = 'pending';
                $defaultPaymentStatus = 'pending';
            }

            $purchaseStatus = $request->filled('status') ? $request->status : $defaultPurchaseStatus;
            $paymentStatus = $request->filled('payment_status') ? $request->payment_status : $defaultPaymentStatus;

            /* ===============================
        6️⃣ UPDATE INVOICE & ITEMS
        =============================== */
            $purchaseInvoice->update([
                'products'         => json_encode($processedProducts),
                'total_amount'     => $totalAmount,
                'discount'         => $request->discount ?? 0,
                'shipping'         => $shippingAmount,
                'grand_total'      => $grandTotal,
                'remaining_amount' => $remainingAfterUpdate,
                'gst_option'       => $request->gst_option === 'with' ? 'with_gst' : 'without_gst',
                'taxes'            => json_encode($request->taxes),
                'status'           => $purchaseStatus,
                'vendor_id'        => $request->vendor_id,
                'bill_no'          => $request->bill_no,
                'purchase_date'    => $purchaseDate,
                'remark'           => $request->remark,
                'payment_mode'     => $request->payment_mode,
                'paid_type'        => $paidType,
                'bank_id'          => $request->bank_id,
                'cash_amount'      => $cashInput,
                'upi_amount'       => $upiInput,
                'amount'           => $directAmount,
                'created_at'       => $purchaseDate,
            ]);

            Purchases::where('invoice_id', $invoice_id)->update([
                'purchase_status' => $purchaseStatus,
                'payment_status'  => $paymentStatus,
                'updated_at'      => now(),
            ]);

            // Update all existing payments to point to the new vendor if changed
            PaymentStore::where('purchase_id', $invoice_id)
                ->where('isDeleted', 0)
                ->update(['user_id' => $request->vendor_id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase updated successfully',
                'remaining_amount' => $remainingAfterUpdate,
                'total_paid' => $totalPaidAfterUpdate,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // public function fetch_purchase_report(Request $request)
    // {
    //     $user         = Auth::guard('api')->user();
    //     $role         = $user->role;
    //     $userBranchId = $user->branch_id;
    //     $userId       = $user->id;
    //     if ($role === 'staff' && $userBranchId) {
    //         $branchIdToUse = $userBranchId;
    //     } elseif ($role === 'admin' && ! empty($request->selectedSubAdminId)) {
    //         $branchIdToUse = $request->selectedSubAdminId;
    //     } else {
    //         $branchIdToUse = $user->id;
    //     }

    //     $query = Purchases::with(['vendor', 'product'])
    //         ->where('purchase_status', 'completed')
    //         ->where('branch_id', $branchIdToUse)
    //         ->where('isDeleted', 0);

    //     // 🔹 If staff, filter using created_by (from PurchaseInvoice)
    //     if ($role === 'staff') {
    //         $query->whereHas('invoice', function ($q) use ($userId) {
    //             $q->where('created_by', $userId);
    //         });
    //     } else {
    //         $query->where('branch_id', $branchIdToUse);
    //     }

    //     // Month / Year filter
    //     $monthParam = $request->month;
    //     $yearParam  = $request->year;

    //     if (! empty($monthParam)) {
    //         $query->whereMonth('created_at', (int) $monthParam);
    //     }
    //     if (! empty($yearParam)) {
    //         $query->whereYear('created_at', (int) $yearParam);
    //     }

    //     // Date / Preset filter (only if month/year not provided)
    //     if (empty($monthParam) && empty($yearParam)) {
    //         $now = Carbon::now();
    //         switch ($request->filter) {
    //             case 'this_week':
    //                 $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
    //                 break;
    //             case 'this_month':
    //                 $query->whereMonth('created_at', $now->month)
    //                     ->whereYear('created_at', $now->year);
    //                 break;
    //             case 'last_6_months':
    //                 $query->whereBetween('created_at', [$now->copy()->subMonths(6)->startOfMonth(), $now->endOfMonth()]);
    //                 break;
    //             case 'this_year':
    //                 $query->whereYear('created_at', $now->year);
    //                 break;
    //             case 'previous_year':
    //                 $query->whereYear('created_at', $now->year - 1);
    //                 break;
    //             default:
    //                 if ($request->from_date && $request->to_date) {
    //                     $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
    //                 }
    //                 break;
    //         }
    //     }

    //     // Optional vendor filter
    //     if ($request->filled('vendor_id')) {
    //         $vendorId = (int) $request->vendor_id;
    //         $query->where('vendor_id', $vendorId);
    //     }

    //     if ($request->filled('category_id')) {
    //         $categoryId = (int) $request->category_id;
    //         $query->whereHas('product', function ($q) use ($categoryId) {
    //             $q->where('category_id', $categoryId);
    //         });
    //     }

    //     $purchases = $query->latest()->get();

    //     // ✅ OPTIMIZED: Load all invoices in one query instead of N+1
    //     $invoiceIds = $purchases->pluck('invoice_id')->filter()->unique()->toArray();
    //     $invoices   = ! empty($invoiceIds)
    //         ? PurchaseInvoice::whereIn('id', $invoiceIds)
    //         ->get()
    //         ->keyBy('id')
    //         : collect();

    //     // ✅ OPTIMIZED: Process purchases with pre-loaded invoices
    //     $processedPurchases = $purchases->map(function ($purchase) use ($invoices) {
    //         $cgstAmount = 0;
    //         $sgstAmount = 0;
    //         $discount   = 0;
    //         $shipping   = 0;
    //         $grandTotal = 0;

    //         if (! empty($purchase->invoice_id)) {
    //             $invoiceId = is_numeric($purchase->invoice_id) ? (int) $purchase->invoice_id : $purchase->invoice_id;
    //             $invoice   = $invoices->get($invoiceId);

    //             if ($invoice) {
    //                 $discount   = $invoice->discount ?? 0;
    //                 $shipping   = $invoice->shipping ?? 0;
    //                 $grandTotal = $invoice->grand_total ?? 0;

    //                 if ($invoice->taxes) {
    //                     $taxes = json_decode($invoice->taxes, true);
    //                     if (is_array($taxes)) {
    //                         foreach ($taxes as $tax) {
    //                             if (isset($tax['name'], $tax['amount'])) {
    //                                 if (strtoupper($tax['name']) === 'CGST') {
    //                                     $cgstAmount = $tax['amount'];
    //                                 } elseif (strtoupper($tax['name']) === 'SGST') {
    //                                     $sgstAmount = $tax['amount'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         $purchase->cgst_amount = $cgstAmount;
    //         $purchase->sgst_amount = $sgstAmount;
    //         $purchase->discount    = $discount;
    //         $purchase->shipping    = $shipping;
    //         $purchase->grand_total = $grandTotal;

    //         return $purchase;
    //     });

    //     // ✅ OPTIMIZED: Cache currency settings
    //     $settings = cache()->remember("settings_branch_{$branchIdToUse}", 300, function () use ($branchIdToUse) {
    //         return DB::table('settings')->where('branch_id', $branchIdToUse)->first();
    //     });
    //     $currencySymbol   = $settings->currency_symbol ?? '₹';
    //     $currencyPosition = $settings->currency_position ?? 'left';

    //     return response()->json([
    //         'status'           => true,
    //         'data'             => $processedPurchases,
    //         'currencySymbol'   => $currencySymbol,
    //         'currencyPosition' => $currencyPosition,
    //     ]);
    // }
    public function fetch_purchase_report(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Determine branch id to use for settings and scoping
        $branchIdToUse = $user->id ?? null;
        $userBranchId = $user->branch_id ?? null;
        $selectedSubAdminId = $request->selectedSubAdminId ?? null;
        if ($user->role === 'staff' && $userBranchId) {
            $branchIdToUse = $userBranchId;
        } elseif ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            $branchIdToUse = $selectedSubAdminId;
        } else {
            $branchIdToUse = $user->id ?? $branchIdToUse;
        }

        // Pagination parameters
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');

        $query = Purchases::with(['vendor', 'vendor.details', 'product', 'invoice'])
            ->where('isDeleted', 0);

        StaffDepartmentScope::applyPurchaseEloquentScope($query, $user, $request->selectedSubAdminId);

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('vendor', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Month / Year filter
        $monthParam = $request->month;
        $yearParam  = $request->year;

        if (! empty($monthParam)) {
            $query->whereMonth('created_at', (int) $monthParam);
        }
        if (! empty($yearParam)) {
            $query->whereYear('created_at', (int) $yearParam);
        }

        // Date / Preset filter (only if month/year not provided)
        if (empty($monthParam) && empty($yearParam)) {
            $now = Carbon::now();
            switch ($request->filter) {
                case 'this_week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [$now->copy()->subMonths(6)->startOfMonth(), $now->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $now->year);
                    break;
                case 'previous_year':
                    $query->whereYear('created_at', $now->year - 1);
                    break;
                default:
                    if ($request->from_date && $request->to_date) {
                        $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                    }
                    break;
            }
        }

        // Optional vendor filter
        if ($request->filled('vendor_id')) {
            $vendorId = (int) $request->vendor_id;
            $query->where('vendor_id', $vendorId);
        }

        // Optional category filter
        if ($request->filled('category_id')) {
            $categoryId = (int) $request->category_id;
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Optional brand filter
        if ($request->filled('brand_id')) {
            $brandId = (int) $request->brand_id;
            $query->whereHas('product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        // Get total count for pagination
        $totalCount = $query->count();

        // Get paginated results
        $purchases = $query->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Load invoices in one query
        $invoiceIds = $purchases->pluck('invoice_id')->filter()->unique()->toArray();
        $invoices = ! empty($invoiceIds)
            ? PurchaseInvoice::whereIn('id', $invoiceIds)->get()->keyBy('id')
            : collect();

        // Process purchases with pre-loaded invoices
        $processedPurchases = $purchases->map(function ($purchase) use ($invoices) {
            $cgstAmount = 0;
            $sgstAmount = 0;
            $discount = 0;
            $shipping = 0;
            $grandTotal = 0;

            if (! empty($purchase->invoice_id)) {
                $invoiceId = is_numeric($purchase->invoice_id) ? (int) $purchase->invoice_id : $purchase->invoice_id;
                $invoice = $invoices->get($invoiceId);

                if ($invoice) {
                    $discount = $invoice->discount ?? 0;
                    $shipping = $invoice->shipping ?? 0;
                    $grandTotal = $invoice->grand_total ?? 0;

                    if ($invoice->taxes) {
                        $taxes = json_decode($invoice->taxes, true);
                        if (is_array($taxes)) {
                            foreach ($taxes as $tax) {
                                if (isset($tax['name'], $tax['amount'])) {
                                    if (strtoupper($tax['name']) === 'CGST') {
                                        $cgstAmount = $tax['amount'];
                                    } elseif (strtoupper($tax['name']) === 'SGST') {
                                        $sgstAmount = $tax['amount'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $purchase->cgst_amount = $cgstAmount;
            $purchase->sgst_amount = $sgstAmount;
            $purchase->discount    = $discount;
            $purchase->shipping    = $shipping;
            $purchase->grand_total = $grandTotal;

            // Expose bill_no directly on the purchase so the frontend can read purchase.bill_no
            $purchase->bill_no = $purchase->invoice->bill_no ?? null;

            return $purchase;
        });

        // Cache currency settings
        $settings = cache()->remember("settings_branch_{$branchIdToUse}", 300, function () use ($branchIdToUse) {
            return DB::table('settings')->where('branch_id', $branchIdToUse)->first();
        });
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => $processedPurchases,
            'currencySymbol' => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'pagination' => [
                'current_page' => (int)$page,
                'last_page' => ceil($totalCount / $perPage),
                'per_page' => (int)$perPage,
                'total' => $totalCount,
                'from' => $processedPurchases->count() > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $processedPurchases->count() > 0 ? (($page - 1) * $perPage) + $processedPurchases->count() : 0,
            ]
        ]);
    }

    public function export_purchase_excel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'purchase_ids'   => 'required|array|min:1',
                'purchase_ids.*' => 'required|integer|exists:purchases,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $purchases = Purchases::with(['vendor', 'product'])
                ->whereIn('id', $request->purchase_ids)
                // ->where('purchase_status', 'completed')
                ->where('isDeleted', 0)
                ->get();

            if ($purchases->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid purchases found for export',
                ], 404);
            }

            // ✅ OPTIMIZED: Load all invoices in one query instead of N+1
            $invoiceIds = $purchases->pluck('invoice_id')->filter()->unique()->toArray();
            $invoices   = ! empty($invoiceIds)
                ? PurchaseInvoice::whereIn('id', $invoiceIds)
                ->get()
                ->keyBy('id')
                : collect();

            // ✅ OPTIMIZED: Process purchases with pre-loaded invoices
            $processedPurchases = $purchases->map(function ($purchase) use ($invoices) {
                $cgstAmount = 0;
                $sgstAmount = 0;
                $discount   = 0;
                $shipping   = 0;
                $grandTotal = 0;

                if ($purchase->invoice_id && ! empty($purchase->invoice_id)) {
                    $invoiceId = is_numeric($purchase->invoice_id) ? (int) $purchase->invoice_id : $purchase->invoice_id;
                    $invoice   = $invoices->get($invoiceId);

                    if ($invoice) {
                        $discount   = $invoice->discount ?? 0;
                        $shipping   = $invoice->shipping ?? 0;
                        $grandTotal = $invoice->grand_total ?? 0;

                        if ($invoice->taxes) {
                            $taxes = json_decode($invoice->taxes, true);
                            if (is_array($taxes)) {
                                foreach ($taxes as $tax) {
                                    if (isset($tax['name']) && isset($tax['amount'])) {
                                        if (strtoupper($tax['name']) === 'CGST') {
                                            $cgstAmount = $tax['amount'];
                                        } elseif (strtoupper($tax['name']) === 'SGST') {
                                            $sgstAmount = $tax['amount'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $purchase->cgst_amount = $cgstAmount;
                $purchase->sgst_amount = $sgstAmount;
                $purchase->discount    = $discount;
                $purchase->shipping    = $shipping;
                $purchase->grand_total = $grandTotal;

                return $purchase;
            });

            // ✅ OPTIMIZED: Cache currency settings
            $settings = cache()->remember("settings_default", 300, function () {
                return DB::table('settings')->first();
            });
            $currencySymbolRaw = $settings->currency_symbol ?? '₹';
            // Decode HTML entities and trim
            $currencySymbol = trim(html_entity_decode($currencySymbolRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            // Fix common mojibake cases (UTF-8 bytes read as Windows-1252)
            $currencySymbol = strtr($currencySymbol, [
                'â‚¬' => '€', // euro
                'Â£'  => '£',    // pound
                'â‚¹' => '₹', // rupee
                'Â¥'  => '¥',    // yen
            ]);
            // As a fallback, try interpreting original as Windows-1252 and converting to UTF-8
            if (! preg_match('/[€£₹¥$]/u', $currencySymbol)) {
                $maybeUtf8 = @mb_convert_encoding($currencySymbolRaw, 'UTF-8', 'Windows-1252');
                if ($maybeUtf8 && preg_match('/[€£₹¥$]/u', $maybeUtf8)) {
                    $currencySymbol = $maybeUtf8;
                }
            }
            $currencyPosition = $settings->currency_position ?? 'left';

            // Map common currency symbols to Unicode codepoints for robust Excel rendering via UNICHAR()
            $symbolToCodepoint = [
                '€' => 8364,
                '£' => 163,
                '₹' => 8377,
                '¥' => 165,
                '$' => 36,
            ];
            $currencyCodepoint = $symbolToCodepoint[$currencySymbol] ?? null;

            // Generate CSV file
            $filename = 'purchase_report_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = storage_path('app/public/exports/' . $filename);

            // Create exports directory if it doesn't exist
            if (! file_exists(storage_path('app/public/exports'))) {
                mkdir(storage_path('app/public/exports'), 0755, true);
            }

            // Create CSV file (UTF-16LE with BOM; include delimiter hint for Excel)
            $file = fopen($filepath, 'w');
            // Write UTF-16LE BOM and delimiter hint (converted)
            fwrite($file, "\xFF\xFE");
            fwrite($file, mb_convert_encoding("sep=,\r\n", 'UTF-16LE', 'UTF-8'));

            // Set headers
            $headers = [
                'Product Name',
                'Vendor Name',
                'Currency',
                'Purchased Amount',
                'Purchased QTY',
                'CGST',
                'SGST',
                'Discount',
                'Shipping',
                'Grand Total',
                'Purchase Date',
            ];

            // Write header row (converted)
            $escapedHeaders = array_map(function ($v) {
                $v = (string) $v;
                return '"' . str_replace('"', '""', $v) . '"';
            }, $headers);
            $headerLine = implode(',', $escapedHeaders) . "\r\n";
            fwrite($file, mb_convert_encoding($headerLine, 'UTF-16LE', 'UTF-8'));

            // Add data rows
            foreach ($processedPurchases as $purchase) {
                // Numeric values (no thousands separator) so Excel parses as numbers
                $amountFormatted     = number_format((float) $purchase->amount_total, 2, '.', '');
                $cgstFormatted       = number_format((float) $purchase->cgst_amount, 2, '.', '');
                $sgstFormatted       = number_format((float) $purchase->sgst_amount, 2, '.', '');
                $discountFormatted   = number_format((float) $purchase->discount, 2, '.', '');
                $shippingFormatted   = number_format((float) $purchase->shipping, 2, '.', '');
                $grandTotalFormatted = number_format((float) $purchase->grand_total, 2, '.', '');

                $row = [
                    $purchase->product ? $purchase->product->name : 'N/A',
                    $purchase->vendor ? $purchase->vendor->name : 'N/A',
                    $currencySymbol,
                    $amountFormatted,
                    (string) $purchase->quantity,
                    $cgstFormatted,
                    $sgstFormatted,
                    $discountFormatted,
                    $shippingFormatted,
                    $grandTotalFormatted,
                    $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i:s') : '',
                ];
                // Escape and write row (converted)
                $escaped = array_map(function ($v) {
                    $v = (string) $v;
                    return '"' . str_replace('"', '""', $v) . '"';
                }, $row);
                $line = implode(',', $escaped) . "\r\n";
                fwrite($file, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }

            // Close CSV file
            fclose($file);

            // Generate download URL
            $downloadUrl = url('storage/exports/' . $filename);

            return response()->json([
                'success'      => true,
                'message'      => 'CSV file generated successfully',
                'filename'     => $filename,
                'download_url' => $downloadUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting purchase CSV: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate CSV file: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Delete purchase order
    public function purchase_delete(Request $request)
    {
        try {
            DB::beginTransaction(); // Start transaction

            // Find the purchase invoice
            $purchase = PurchaseInvoice::find($request->id);
            if (! $purchase) {
                return response()->json(['status' => false, 'message' => 'Purchase order not found.'], 404);
            }

            // Soft delete all purchase rows for this invoice
            Purchases::where('invoice_id', $request->id)
                ->update(['isDeleted' => 1]);

            // Soft delete the purchase invoice
            $purchase->isDeleted = 1;
            $purchase->save();

            DB::commit(); // Commit transaction

            return response()->json([
                'status'  => true,
                'message' => 'Purchase order deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            return response()->json([
                'status'  => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Purchases::with(['vendor', 'product']);

        // Filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $purchases = $query->latest()->get();
        $vendors   = \App\Models\User::where('role', 'vendor')->get();

        return view('purchase.purchasereport', compact('purchases', 'vendors'));
    }

    // public function getHistory1($job_card_id)
    // {
    //     $history = PaymentStore::where('purchase_id', $job_card_id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $history,
    //     ]);
    // }
    public function getHistory1($job_card_id)
    {
        // All payments for this purchase
        $history = PaymentStore::where('purchase_id', $job_card_id)
            ->where('isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Purchase invoice
        $purchase = PurchaseInvoice::findOrFail($job_card_id);

        $totalPaid  = $history->sum('payment_amount');
        $orderTotal = $purchase->grand_total ?? 0;

        $totalReturn = \App\Models\PurchaseReturn::where('purchase_id', $job_card_id)
            ->where('isDeleted', 0)
            ->sum('total_amount');

        // ✅ Dynamic calculations (accounts for returns)
        $extraPaid = max(0, $totalPaid + $totalReturn - $orderTotal);
        $remaining = max(0, $orderTotal - $totalPaid - $totalReturn);

        return response()->json([
            'status'  => 'success',
            'data'    => $history,
            'summary' => [
                'order_total' => $orderTotal,
                'total_paid'  => $totalPaid,
                'remaining'   => $remaining,
                'extra_paid'  => $extraPaid,
                'total_return' => $totalReturn,
            ],
        ]);
    }


    public function make_payment(Request $request)
    {
        // dd('Payment endpoint hit', $request->all());
        $user_id = Auth::guard('api')->user()->id;

        $request->validate([
            'purchase_id'    => 'nullable|integer',
            'payment_amount' => 'nullable|numeric',
            'payment_date'   => 'nullable|date',
            'payment_type'   => 'nullable|string',
            'emi_month'      => 'nullable|integer',
            'pending_date'   => 'nullable|date',
            'new_emi_value'  => 'nullable',
            'emi_paid_value' => 'nullable|numeric',
            'remarks'       => 'nullable|string',
        ]);

        if ($request->filled('purchase_id')) {
            // ✅ Find purchase (JobCard)
            $jobCard = PurchaseInvoice::find($request->purchase_id);
            if (! $jobCard) {
                return response()->json(['status' => 'error', 'message' => 'Purchase not found'], 404);
            }

            // ✅ Calculate current total paid from database
            $totalPaidSoFar = PaymentStore::where('purchase_id', $jobCard->id)
                ->where('isDeleted', 0)
                ->sum('payment_amount');

            $totalReturnSoFar = \App\Models\PurchaseReturn::where('purchase_id', $jobCard->id)
                ->where('isDeleted', 0)
                ->sum('total_amount');

            // ✅ Calculate total payment amount for this request
            $paymentAmount = $request->emi_total_new ?? $request->emi_total ?? $request->amount ?? $request->upi_online_amount ?? 0;

            if ($request->filled('cash_amount') && $request->filled('online_amount')) {
                $paymentAmount = (float) $request->cash_amount + (float) $request->online_amount;
            } elseif ($request->filled('fully_cash_amount') && $request->filled('full_online_amount')) {
                $paymentAmount = (float) $request->fully_cash_amount + (float) $request->full_online_amount;
            } elseif ($request->cashAmount) {
                $paymentAmount = $request->cashAmount;
            } elseif ($request->upi_online_amount) {
                $paymentAmount = $request->upi_online_amount;
            } elseif ($request->emi_monthly) {
                $paymentAmount = $request->emi_monthly;
            }

            $grandTotal   = $jobCard->grand_total ?? 0;
            $newTotalPaid = $totalPaidSoFar + $paymentAmount;
            $newRemaining = max(0, $grandTotal - $newTotalPaid - $totalReturnSoFar);

            // ✅ Determine type
            if (
                in_array($request->paid_type, ['cash_partially']) ||
                in_array($request->online_type, ['online_partially']) ||
                in_array($request->cash_online_type, ['cash_online_partially'])
            ) {
                $type = 'partially';
            } elseif (
                in_array($request->paid_type, ['cash_fully']) ||
                in_array($request->online_type, ['online_fully']) ||
                in_array($request->cash_online_type, ['cash_online_fully']) ||
                $request->payment_type === 'fully'
            ) {
                $type = 'fully';
            } elseif (in_array($request->emi_type, ['emi'])) {
                $type = 'emi';
            } else {
                $type = 'fully';
            }

            $payments = [];

            // ✅ Handle cash_online separate entries
            if (in_array($request->cash_online_type, ['cash_online_partially', 'cash_online_fully'])) {
                // 1️⃣ Cash entry
                $cashValue = $request->cash_online_type === 'cash_online_partially'
                    ? ($request->cash_amount ?? 0)
                    : ($request->fully_cash_amount ?? 0);

                if ($cashValue > 0) {
                    $payments[] = PaymentStore::create([
                        'user_id'          => $user_id,
                        'purchase_id'      => $jobCard->id,
                        'payment_amount'   => $cashValue,
                        'remaining_amount' => $newRemaining,
                        'payment_method'   => 'cash',
                        'payment_date'     => now(),
                        'payment_type'     => $type,
                        'cash_amount'      => $cashValue,
                        'upi_amount'       => 0,
                        'status'           => 'debit',
                        'bank_id'          => $request->bank_id,
                        'remarks'          => $request->remarks ?? '',
                        'emi_month'        => $request->emi_month ?? 1,
                        'isDeleted'        => 0,
                    ]);
                }

                // 2️⃣ Online entry
                $onlineValue = $request->cash_online_type === 'cash_online_partially'
                    ? ($request->online_amount ?? 0)
                    : ($request->full_online_amount ?? 0);

                if ($onlineValue > 0) {
                    $payments[] = PaymentStore::create([
                        'user_id'          => $user_id,
                        'purchase_id'      => $jobCard->id,
                        'payment_amount'   => $onlineValue,
                        'remaining_amount' => $newRemaining,
                        'payment_method'   => 'online',
                        'payment_date'     => now(),
                        'payment_type'     => $type,
                        'cash_amount'      => 0,
                        'upi_amount'       => $onlineValue,
                        'status'           => 'debit',
                        'bank_id'          => $request->bank_id,
                        'remarks'          => $request->remarks ?? '',
                        'emi_month'        => $request->emi_month ?? 1,
                        'isDeleted'        => 0,
                    ]);
                }
            } else {
                // ✅ Default single payment record
                $payments[] = PaymentStore::create([
                    'user_id'          => $user_id,
                    'purchase_id'      => $jobCard->id,
                    'payment_amount'   => $paymentAmount,
                    'remaining_amount' => $newRemaining,
                    'payment_method'   => $request->payment_method ?? $request->payment_type ?? '',
                    'payment_date'     => now(),
                    'payment_type'     => $type,
                    'cash_amount'      => $request->cash_amount ?? 0,
                    'upi_amount'       => $request->online_amount ?? 0,
                    'status'           => 'debit',
                    'bank_id'          => $request->bank_id,
                    'remarks'          => $request->remarks ?? '',
                    'emi_month'        => $request->emi_month ?? 1,
                    'isDeleted'        => 0,
                ]);
            }

            // ✅ Update JobCard remaining and paid columns
            $jobCard->update([
                'remaining_amount' => $newRemaining,
                'paid'             => $newTotalPaid,
            ]);

            // ✅ Determine and update status
            // $paymentStatus  = 'unpaid';
            // $purchaseStatus = 'pending';

            // if ($newRemaining <= 0) {
            //     $paymentStatus  = 'paid';
            //     $purchaseStatus = 'completed';
            // } elseif ($newRemaining > 0 && $newRemaining < ($jobCard->grand_total ?? 0)) {
            //     $paymentStatus = 'partial';
            // }
            if ($newRemaining <= 0 && $paymentAmount > 0) {
                $paymentStatus  = 'completed';
                $purchaseStatus = 'completed';
            } elseif ($newRemaining > 0 && $paymentAmount > 0) {
                $paymentStatus  = 'partially';
                $purchaseStatus = 'partially';
            } else {
                $paymentStatus  = 'pending';
                $purchaseStatus = 'pending';
            }

            Purchases::where('invoice_id', $jobCard->id)->update([
                'payment_status'  => $paymentStatus,
                'purchase_status' => $purchaseStatus,
                'updated_at'      => now(),
            ]);

            PurchaseInvoice::where('id', $jobCard->id)->update([
                // 'payment_status'  => $paymentStatus,
                'status'     => $purchaseStatus,
                'updated_at' => now(),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Payment submitted successfully.',
                'data'    => $payments,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Purchase ID is required'], 400);
    }

    public function paymentHistory($purchaseId)
    {
        $history = PaymentStore::where('purchase_id', $purchaseId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $history,
        ]);
    }

    public function export_purchase(Request $request)
    {
        $user      = Auth::guard('api')->user();
        $branch_id = StaffDepartmentScope::resolveBranchId($user, $request->selectedSubAdminId);

        $query = DB::table('purchase_invoice')
            ->join('purchases', function ($join) use ($branch_id) {
                $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                    ->where('purchases.isDeleted', '=', 0)
                    ->where('purchases.branch_id', '=', $branch_id);
            })
            ->join('users', 'purchases.vendor_id', '=', 'users.id')
            ->join('products', 'purchases.item', '=', 'products.id')
            ->select(
                'purchase_invoice.id',
                'users.name as vendor_name',
                'purchase_invoice.bill_no',
                'purchase_invoice.grand_total',
                'purchase_invoice.remaining_amount',
                'purchases.purchase_status',
                'purchases.payment_status',
                'purchase_invoice.created_at as date',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as product_names"),
                DB::raw("GROUP_CONCAT(purchases.price SEPARATOR ', ') as product_prices"),
                DB::raw("GROUP_CONCAT(purchases.quantity SEPARATOR ', ') as product_quantities"),
                DB::raw("(SELECT COUNT(*) FROM payment_store
                  WHERE payment_store.purchase_id = purchase_invoice.id
                  AND payment_store.isDeleted = 0) as has_payment")
            )
            ->where('purchase_invoice.isDeleted', '=', 0);

        StaffDepartmentScope::applyPurchaseCreatedByScope($query, $user, 'purchases.created_by');

        // ✅ Date filter
        if ($request->has('date') && ! empty($request->date)) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
                $query->whereDate('purchases.created_at', $date);
            } catch (\Exception $e) {
                $query->whereDate('purchases.created_at', $request->date);
            }
        }

        // ✅ Month filter
        if ($request->filled('month') && $request->month !== 'all') {
            $query->whereMonth('purchases.created_at', $request->month);
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('purchases.created_at', $request->year);
        }
        // ✅ Vendor filter
        if ($request->has('customer_id') && ! empty($request->customer_id)) {
            $query->where('users.name', $request->customer_id);
        }

        $purchases = $query
            ->groupBy(
                'purchase_invoice.id',
                'users.name',
                'purchase_invoice.bill_no',
                'purchase_invoice.grand_total',
                'purchase_invoice.remaining_amount',
                'purchases.purchase_status',
                'purchases.payment_status',
                'purchase_invoice.created_at'
            )
            ->orderBy('purchase_invoice.id', 'desc')
            ->get();

        if ($purchases->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No purchase data found for selected month and year.'
            ]);
        }

        // ✅ Add pending amount
         foreach ($purchases as $purchase) {
            $payment = \App\Models\PaymentStore::where('purchase_id', $purchase->id)
                ->where('isDeleted', 0)
                ->orderBy('id', 'desc')
                ->first();
             $purchase->pending_amount = $this->resolvePurchaseRemainingAmount(
                $purchase->remaining_amount ?? 0,
                $payment->remaining_amount ?? null
            );
             }

        // ✅ OPTIMIZED: Cache currency settings
        $settings = cache()->remember("settings_branch_{$branch_id}", 300, function () use ($branch_id) {
            return DB::table('settings')->where('branch_id', $branch_id)->first();
        });
        $currencySymbolRaw = $settings->currency_symbol ?? '₹';
        $currencySymbol    = trim(html_entity_decode($currencySymbolRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $currencyPosition  = $settings->currency_position ?? 'left';

        $formatIndian = function ($num) {
            $num = (float)$num;
            $explode = explode(".", number_format($num, 2, '.', ''));
            $whole = $explode[0];
            $decimal = $explode[1];

            $lastThree = substr($whole, -3);
            $restUnits = substr($whole, 0, -3);
            if ($restUnits != '') {
                $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
                $whole = $restUnits . "," . $lastThree;
            }
            return $whole . "." . $decimal;
        };

        // ✅ Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Bill No');
        $sheet->setCellValue('B1', 'Vendor');
        $sheet->setCellValue('C1', 'Date');
        // $sheet->setCellValue('D1', 'Products');
        // $sheet->setCellValue('E1', 'Quantities');
        // $sheet->setCellValue('F1', 'Prices');
        // $sheet->setCellValue('D1', 'Purchase Status');
        $sheet->setCellValue('D1', 'Payment Status');
         $sheet->setCellValue('E1', 'Grand Total');
        $sheet->setCellValue('F1', 'Remaining Amount');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $row = 2;

        foreach ($purchases as $purchase) {
            // $sheet->setCellValue('A' . $row, $purchase->bill_no);
            $sheet->setCellValueExplicit(
                'A' . $row,
                $purchase->bill_no,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $sheet->setCellValue('B' . $row, $purchase->vendor_name);
            $sheet->setCellValue('C' . $row, \Carbon\Carbon::parse($purchase->date)->format('d-m-y'));
            // $sheet->setCellValue('D' . $row, $purchase->product_names);
            // $sheet->setCellValue('E' . $row, $purchase->product_quantities);
            // $sheet->setCellValue('F' . $row, $purchase->product_prices);
            // $sheet->setCellValue('D' . $row, ucfirst($purchase->purchase_status));
            $sheet->setCellValue('D' . $row, ucfirst($purchase->payment_status));
             $sheet->setCellValue('E' . $row, $currencyPosition === 'left'
                ? $currencySymbol . ' ' . $formatIndian($purchase->grand_total)
                : $formatIndian($purchase->grand_total) . ' ' . $currencySymbol);
            $sheet->setCellValue('F' . $row, $currencyPosition === 'left'
                ? $currencySymbol . ' ' . $formatIndian($purchase->pending_amount)
                : $formatIndian($purchase->pending_amount) . ' ' . $currencySymbol);
            $row++;
        }

        // $writer   = new Xlsx($spreadsheet);
        // $fileName = 'Purchases_' . date('Ymd_His') . '.xlsx';

        // return response()->streamDownload(function () use ($writer) {
        //     $writer->save('php://output');
        // }, $fileName, [
        //     'Content-Type'                  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        //     'Access-Control-Expose-Headers' => 'Content-Disposition',
        // ]);
        $writer       = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename     = 'Purchases_' . date('Ymd_His') . '.xlsx';
        $relativePath = 'exports/' . $filename;

        // Save temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);
        Storage::disk('public')->put($relativePath, file_get_contents($temp_file));
        unlink($temp_file);

        // Generate public URL

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        return response()->json([
            'status'    => true,
            'message'   => 'Purchases Excel generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $filename,
        ]);
    }

    public function export_purchase_pdf(Request $request)
    {
        $user      = Auth::guard('api')->user();
        $branch_id = StaffDepartmentScope::resolveBranchId($user, $request->selectedSubAdminId);

        $query = DB::table('purchase_invoice')
            ->join('purchases', function ($join) use ($branch_id) {
                $join->on('purchase_invoice.id', '=', 'purchases.invoice_id')
                    ->where('purchases.isDeleted', '=', 0)
                    ->where('purchases.branch_id', '=', $branch_id);
            })
            ->join('users', 'purchases.vendor_id', '=', 'users.id')
            ->join('products', 'purchases.item', '=', 'products.id')
            ->select(
                'purchase_invoice.id',
                'users.name as vendor_name',
                'purchase_invoice.bill_no',
                'purchase_invoice.grand_total',
                'purchase_invoice.remaining_amount',
                'purchases.purchase_status',
                'purchases.payment_status',
                'purchase_invoice.created_at as date',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as product_names"),
                DB::raw("GROUP_CONCAT(purchases.price SEPARATOR ', ') as product_prices"),
                DB::raw("GROUP_CONCAT(purchases.quantity SEPARATOR ', ') as product_quantities"),
                DB::raw("(SELECT COUNT(*) FROM payment_store
                  WHERE payment_store.purchase_id = purchase_invoice.id
                  AND payment_store.isDeleted = 0) as has_payment")
            )
            ->where('purchase_invoice.isDeleted', '=', 0);

        StaffDepartmentScope::applyPurchaseCreatedByScope($query, $user, 'purchases.created_by');

        // ✅ Date filter
        if ($request->has('date') && ! empty($request->date)) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
                $query->whereDate('purchases.created_at', $date);
            } catch (\Exception $e) {
                $query->whereDate('purchases.created_at', $request->date);
            }
        }

        // ✅ Month filter
        if ($request->filled('month') && $request->month !== 'all') {
            $query->whereMonth('purchases.created_at', $request->month);
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('purchases.created_at', $request->year);
        }
        // ✅ Vendor filter
        if ($request->has('customer_id') && ! empty($request->customer_id)) {
            $query->where('users.name', $request->customer_id);
        }

        $purchases = $query
            ->groupBy(
                'purchase_invoice.id',
                'users.name',
                'purchase_invoice.bill_no',
                'purchase_invoice.grand_total',
                'purchase_invoice.remaining_amount',
                'purchases.purchase_status',
                'purchases.payment_status',
                'purchase_invoice.created_at'
            )
            ->orderBy('purchase_invoice.id', 'desc')
            ->get();

        if ($purchases->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No purchase data found for selected filters.'
            ]);
        }
        // ✅ OPTIMIZED: Bulk load payments instead of N+1 queries
        $purchaseIds = $purchases->pluck('id')->toArray();
        $payments    = ! empty($purchaseIds)
            ? PaymentStore::whereIn('purchase_id', $purchaseIds)
            ->where('isDeleted', 0)
            ->orderBy('purchase_id')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('purchase_id')
            ->map(function ($group) {
                return $group->first();
            })
            : collect();

        $totalPending     = 0;
        $totalGrandAmount = 0;
        foreach ($purchases as $purchase) {
            $payment                  = $payments->get($purchase->id);
          $purchase->pending_amount = $this->resolvePurchaseRemainingAmount(
                $purchase->remaining_amount ?? 0,
                $payment->remaining_amount ?? null
            );
                        $totalPending += $purchase->pending_amount;
            $totalGrandAmount += $purchase->grand_total;
        }

        // ✅ OPTIMIZED: Cache currency settings
        $settings = cache()->remember("settings_branch_{$branch_id}", 300, function () use ($branch_id) {
            return DB::table('settings')->where('branch_id', $branch_id)->first();
        });
        $currencySymbolRaw = $settings->currency_symbol ?? '₹';
        $currencySymbol    = trim(html_entity_decode($currencySymbolRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $currencyPosition  = $settings->currency_position ?? 'left';

        // Get user info for PDF
        $userName = $user->name ?? 'N/A';
        $gstNum   = $user->gst_number ?? 'N/A';

        // ✅ Generate PDF using a Blade view
        $pdf = Pdf::loadView('purchase.purchase_pdf', [
            'purchases'        => $purchases,
            'totalPending'     => $totalPending,
            'totalGrandAmount' => $totalGrandAmount,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'settings'         => $settings,
            'userName'         => $userName,
            'gstNum'           => $gstNum,
        ]);

        // 🔹 Save PDF to storage
        $fileName     = 'Purchases_' . now()->format('Ymd_His') . '.pdf';
        $relativePath = 'purchase-reports/' . $fileName;
        Storage::disk('public')->put($relativePath, $pdf->output());

        // 🔹 Generate full public URL

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        // 🔹 Return JSON response with PDF info
        return response()->json([
            'status'    => true,
            'message'   => 'Purchase PDF generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function export_purchases_report_pdf_api(Request $request)
    {
        try {
            $user               = Auth::guard('api')->user();
            $branchId           = $user->id ?? null;
            $UserBranchId       = $user->branch_id ?? null;
            $userRole           = $user->role ?? '';
            $selectedSubAdminId = $request->selectedSubAdminId ?? null;

            // ✅ Determine branch_id based on role
            if ($userRole === 'sub-admin') {
                $branchId = $branchId;
            } elseif ($userRole === 'admin' && $selectedSubAdminId) {
                $branchId = $selectedSubAdminId;
            } elseif ($userRole === 'staff') {
                $branchId = $UserBranchId;
            }

            // ✅ Collect IDs from form-data (ids[] = 1, ids[] = 2, etc.)
            $idsArray = $request->input('ids', []); // ensures an array, even if empty
            // ✅ Store all IDs into one variable (as comma-separated string, if needed)
            $idsString = implode(',', $idsArray);
            // dd($idsArray);

            if (empty($idsArray)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No purchase IDs provided.',
                ]);
            }

            // ✅ Fetch purchase data
            $purchases = Purchases::with('product', 'invoice', 'vendor')
                ->whereIn('id', $idsArray)
                ->where('branch_id', $branchId)
                ->get();

            if ($purchases->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No purchase data found.',
                ]);
            }

            // ✅ OPTIMIZED: Cache settings
            $setting = cache()->remember("setting_branch_{$branchId}", 300, function () use ($branchId) {
                return Setting::where('branch_id', $branchId)->first();
            });
            $subtotalRaw = (float) $purchases->sum('amount_total');

            $discountPercent   = 0.0;
            $discountAmountRaw = ($discountPercent / 100.0) * $subtotalRaw;
            $afterDiscountRaw  = $subtotalRaw - $discountAmountRaw;

            $invoiceIds  = [];
            $shippingRaw = 0.0;
            foreach ($purchases as $purchase) {
                if ($purchase->invoice && ! in_array($purchase->invoice->id, $invoiceIds)) {
                    $invoiceIds[] = $purchase->invoice->id;
                    $shippingRaw += (float) ($purchase->invoice->shipping ?? 0);
                }
            }

            $taxRates = TaxRate::where('status', 'active')
                ->where('branch_id', $branchId)
                ->where('isDeleted', 0)
                ->get();

            $taxDetails     = [];
            $formatCurrency = function ($amt) use ($setting) {
                return $setting->currency_position === 'right'
                    ? number_format($amt, 2) . $setting->currency_symbol
                    : $setting->currency_symbol . number_format($amt, 2);
            };

            foreach ($taxRates as $tax) {
                $amount       = ($tax->tax_rate / 100.0) * $afterDiscountRaw;
                $taxDetails[] = [
                    'name'             => $tax->tax_name,
                    'rate'             => $tax->tax_rate,
                    'amount'           => $amount,
                    'formatted_amount' => $formatCurrency($amount),
                ];
            }

            $grandTotalRaw = $afterDiscountRaw + $shippingRaw + collect($taxDetails)->sum('amount');

            $invoiceRecord = PurchaseInvoice::whereIn('id', $idsArray)
                ->where('branch_id', $branchId)
                ->first();

            $invoice = (object) [
                'invoice_number'   => $invoiceRecord->invoice_number ?? 'PR-' . now()->format('YmdHis'),
                'created_at'       => $invoiceRecord->created_at ?? now()->format('Y-m-d H:i:s'),
                'paid'             => $invoiceRecord->paid ?? false,
                'status'           => $invoiceRecord->status ?? 'completed',
                'remaining_amount' => $invoiceRecord->remaining_amount ?? 0,
                'gst_option'       => $invoiceRecord->gst_option ?? 'without_gst',
            ];

            $compenyinfo      = $setting;
            $currencySymbol   = $compenyinfo->currency_symbol ?? '₹';
            $currencyPosition = $compenyinfo->currency_position ?? 'left';
            $vendor           = $purchases->first()->vendor ?? null;

            $pdfData = [
                'invoice'          => $invoice,
                'vendor'           => [
                    'name'    => $vendor->name ?? 'Walk-in Vendor',
                    'email'   => $vendor->email ?? '',
                    'phone'   => $vendor->phone ?? '',
                    'address' => $vendor->address ?? '',
                ],
                'purchases'        => $purchases,
                'currencySymbol'   => $currencySymbol,
                'currencyPosition' => $currencyPosition,
                'setting'          => $setting,
                'subtotal'         => $formatCurrency($subtotalRaw),
                'discount'         => $discountPercent,
                'discountAmount'   => $formatCurrency($discountAmountRaw),
                'afterDiscount'    => $formatCurrency($afterDiscountRaw),
                'shipping'         => $formatCurrency($shippingRaw),
                'taxDetails'       => $taxDetails,
                'grandTotal'       => $formatCurrency($grandTotalRaw),
                'payment_status'   => ucfirst($purchases->first()->payment_status ?? 'Pending'),
                'payment_method'   => ucfirst($purchases->first()->payment_method ?? 'N/A'),
            ];

            // ✅ Generate the PDF
            $pdf = PDF::loadView('purchase.purchase-report-pdf', $pdfData)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont'          => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                ]);

            // ✅ Generate a unique filename
            $filename     = 'purchase_report_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'purchase-reports/' . $filename;

            // ✅ Save to storage/public/purchase-reports/
            Storage::disk('public')->put($relativePath, $pdf->output());

            // ✅ Build public URL
            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            return response()->json([
                'status'    => true,
                'message'   => 'Purchase report PDF generated successfully.',
                'file_url'  => $fileUrl,
                'file_name' => $filename,
                'ids_used'  => $idsString, // ✅ shows all IDs used
            ]);
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Failed to generate Purchase Report PDF.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function purchaseProductChart(Request $request)
    {
        $user       = Auth::guard('api')->user();
        $subAdminId = $request->selectedSubAdminId;

        $query = Purchases::with('product')
            ->where('isDeleted', 0);

        StaffDepartmentScope::applyPurchaseEloquentScope($query, $user, $subAdminId);

        // 🔹 Apply filters
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        // dd($request->category_id);
        // 🔹 Aggregate totals
        $data = $query->selectRaw('item, SUM(quantity) as total_qty, SUM(amount_total) as total_amount')
            ->groupBy('item')
            ->with('product:id,name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $labels     = $data->pluck('product.name')->toArray();
        $totals     = $data->pluck('total_qty')->toArray();
        $grandTotal = $data->sum('total_amount');

        return response()->json([
            'status'      => true,
            'labels'      => $labels,
            'totals'      => $totals,
            'grand_total' => $grandTotal,
        ]);
    }

    public function view_purchase_report(Request $request)
    {
        try {
            $ids                = $request->input('ids');
            $selectedSubAdminId = $request->input('selectedSubAdminId');
            $branchIdToUse      = $request->input('branch');

            if (empty($ids)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No purchase IDs provided.',
                ], 400);
            }

            // Convert IDs array to string if needed
            $idsString = is_array($ids) ? implode(',', $ids) : $ids;

            $authUser = Auth::guard('api')->user();

            // 🔹 Determine branch ID logic
            if ($authUser) {
                if ($authUser->role === 'staff' && $authUser->branch_id) {
                    $branchIdToUse = $authUser->branch_id;
                } elseif ($authUser->role === 'admin' && ! empty($selectedSubAdminId)) {
                    $branchIdToUse = $selectedSubAdminId;
                } else {
                    $branchIdToUse = $authUser->id;
                }
            } else {
                // 🔹 No authentication — require branch ID from frontend
                if (! empty($selectedSubAdminId)) {
                    $branchIdToUse = $selectedSubAdminId;
                } elseif (! empty($branchIdToUse)) {
                    $branchIdToUse = $branchIdToUse;
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Branch ID missing (unauthenticated request).',
                    ], 400);
                }
            }

            // 🔹 Generate report view URL
            $reportUrl = url('purchase/report/view-page?ids=' . $idsString . '&branch=' . $branchIdToUse);

            return response()->json([
                'status'    => true,
                'message'   => 'Purchase report link generated successfully.',
                'view_link' => $reportUrl,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to generate purchase report link.',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Import purchase list rows from CSV/XLS/XLSX.
     */
    public function importPurchaseList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please upload a valid CSV or Excel file.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $authUser = Auth::user() ?: Auth::guard('api')->user();
        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $branchId = $this->resolvePurchaseImportBranchId($authUser, $request->input('selectedSubAdminId'));
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return response()->json([
                'status' => false,
                'message' => 'The uploaded file does not contain import rows.',
            ], 422);
        }

        $requiredHeaders = ['supplier'];
        $headers = [];
        $headerRowNumber = null;

        foreach ($rows as $rowNumber => $candidateHeaderRow) {
            $candidateHeaders = [];
            $headerOccurrences = [];
            foreach ($candidateHeaderRow as $column => $heading) {
                $normalized = $this->normalizePurchaseImportHeader((string) $heading);
                if ($normalized !== '') {
                    $headerOccurrences[$normalized] = ($headerOccurrences[$normalized] ?? 0) + 1;
                    if ($normalized === 'discount' && $headerOccurrences[$normalized] === 1) {
                        $normalized = 'discount_amount';
                    } elseif ($normalized === 'discount') {
                        $normalized = 'discount_percent';
                    }
                    $candidateHeaders[$column] = $normalized;
                }
            }

            $matchedRequiredHeaders = array_intersect($requiredHeaders, array_values($candidateHeaders));
            if (count($matchedRequiredHeaders) === count($requiredHeaders)) {
                $headers = $candidateHeaders;
                $headerRowNumber = $rowNumber;
                break;
            }
        }

        if ($headerRowNumber === null) {
            return response()->json([
                'status' => false,
                'message' => 'Could not find the header row. Please include Supplier column.',
            ], 422);
        }

        $rows = array_filter(
            $rows,
            static fn ($rowNumber) => $rowNumber > $headerRowNumber,
            ARRAY_FILTER_USE_KEY
        );
        $imported = 0;
        $updated = 0;
        $suppliersCreated = 0;
        $suppliersUpdated = 0;
        $itemsImported = 0;
        $skipped = 0;
        $errors = [];
        $touchedInvoiceIds = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index;
                $mapped = $this->mapPurchaseImportRow($row, $headers);

                if ($this->isEmptyPurchaseImportRow($mapped)) {
                    continue;
                }

                $supplierName = trim((string) ($mapped['supplier'] ?? ''));
                $docNo = trim((string) ($mapped['doc_no'] ?? ''));

                if ($supplierName === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Supplier is required.";
                    continue;
                }

                if ($docNo === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Doc.No is required for purchase import.";
                    continue;
                }

                $docDate = $this->parsePurchaseImportDate($mapped['doc_date'] ?? null);
                if (! $docDate) {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Invalid Doc.Date.";
                    continue;
                }

                $refDocDate = null;
                if (trim((string) ($mapped['ref_doc_date'] ?? '')) !== '') {
                    $refDocDate = $this->parsePurchaseImportDate($mapped['ref_doc_date']);
                    if (! $refDocDate) {
                        $skipped++;
                        $errors[] = "Row {$lineNumber}: Invalid Ref.Doc.Date.";
                        continue;
                    }
                }

                $supplierResult = $this->upsertPurchaseImportSupplier($supplierName, $branchId, $authUser);
                $supplier = $supplierResult['supplier'];

                if ($supplierResult['created']) {
                    $suppliersCreated++;
                } elseif ($supplierResult['updated']) {
                    $suppliersUpdated++;
                }

                $docValue = $this->parsePurchaseImportAmount($mapped['doc_value'] ?? 0);
                $cgstAmount = $this->parsePurchaseImportAmount($mapped['cgst_amount'] ?? 0);
                $sgstAmount = $this->parsePurchaseImportAmount($mapped['sgst_amount'] ?? 0);
                $igstAmount = $this->parsePurchaseImportAmount($mapped['igst_amount'] ?? 0);
                $statusRaw = trim((string) ($mapped['status'] ?? ''));
                $purchaseStatus = $this->mapPurchaseImportStatus($statusRaw);
                $paymentStatus = $purchaseStatus === 'completed' ? 'completed' : 'pending';

                $invoiceData = [
                    'vendor_id' => $supplier->id,
                    'bill_no' => $docNo,
                    'total_amount' => $docValue,
                    'grand_total' => $docValue,
                    'remaining_amount' => $paymentStatus === 'completed' ? 0 : $docValue,
                    'gst_option' => ($cgstAmount + $sgstAmount + $igstAmount) > 0 ? 'with_gst' : 'without_gst',
                    'status' => $purchaseStatus,
                    'branch_id' => $branchId,
                    'created_by' => $authUser->id,
                    'purchase_date' => $docDate->toDateString(),
                    'import_sn' => $this->parsePurchaseImportInteger($mapped['sn'] ?? null),
                    'import_supplier' => $supplierName,
                    'import_doc_no' => $docNo,
                    'import_doc_date' => $docDate->toDateString(),
                    'import_ref_doc_no' => trim((string) ($mapped['ref_doc_no'] ?? '')) ?: null,
                    'import_ref_doc_date' => $refDocDate ? $refDocDate->toDateString() : null,
                    'import_doc_value' => $docValue,
                    'import_status' => $statusRaw ?: null,
                    'import_purchase_type' => trim((string) ($mapped['purchase_type'] ?? '')) ?: null,
                    'import_source' => 'purchase_list_import',
                    'created_at' => $docDate->copy()->setTime(0, 0, 0),
                    'updated_at' => now('Asia/Kolkata'),
                ];

                $existingInvoice = PurchaseInvoice::where('bill_no', $docNo)
                    ->where('branch_id', $branchId)
                    ->where('isDeleted', 0)
                    ->first();

                if ($existingInvoice) {
                    $existingInvoice->update($invoiceData);
                    $purchaseInvoice = $existingInvoice;
                    $updated++;
                } else {
                    $purchaseInvoice = PurchaseInvoice::create(array_merge([
                        'invoice_number' => $this->generatePurchaseImportInvoiceNumber(),
                    ], $invoiceData));
                    $imported++;
                }

                if ($this->storePurchaseImportItem($mapped, $purchaseInvoice, $supplier, $branchId, $authUser, $purchaseStatus, $paymentStatus, $docDate)) {
                    $itemsImported++;
                    $touchedInvoiceIds[$purchaseInvoice->id] = true;
                }
            }

            $this->refreshPurchaseImportInvoiceProducts(array_keys($touchedInvoiceIds));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Purchase list import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Import failed. Please check the file and try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase list imported successfully.',
            'imported' => $imported,
            'updated' => $updated,
            'suppliers_created' => $suppliersCreated,
            'suppliers_updated' => $suppliersUpdated,
            'items_imported' => $itemsImported,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    private function storePurchaseImportItem(array $mapped, PurchaseInvoice $purchaseInvoice, User $supplier, int $branchId, User $authUser, string $purchaseStatus, string $paymentStatus, Carbon $docDate): bool
    {
        $itemCode = trim((string) ($mapped['item_model_code'] ?? ''));
        $itemName = trim((string) ($mapped['item_model'] ?? ''));
        $qty = $this->parsePurchaseImportAmount($mapped['qty'] ?? 0);
        $freeQty = $this->parsePurchaseImportAmount($mapped['free_qty'] ?? 0);
        $rate = $this->parsePurchaseImportAmount($mapped['rate'] ?? 0);
        $lineAmount = $this->parsePurchaseImportAmount($mapped['line_amount'] ?? 0);
        $discountPercent = $this->parsePurchaseImportAmount($mapped['discount_percent'] ?? 0);
        $discountAmount = $this->parsePurchaseImportAmount($mapped['discount_amount'] ?? 0);
        $taxableAmount = $this->parsePurchaseImportAmount($mapped['taxable_amount'] ?? 0);
        $cgstAmount = $this->parsePurchaseImportAmount($mapped['cgst_amount'] ?? 0);
        $sgstAmount = $this->parsePurchaseImportAmount($mapped['sgst_amount'] ?? 0);
        $igstAmount = $this->parsePurchaseImportAmount($mapped['igst_amount'] ?? 0);
        $netAmount = $this->parsePurchaseImportAmount($mapped['net_amount'] ?? 0);
        $imeiNo = trim((string) ($mapped['imei_no'] ?? '')) ?: null;
        $activationDate = null;

        // Some purchase exports label both discount columns simply as "DISC".
        // Keep percentage values within their valid range and recover gracefully
        // when the amount and percentage columns appear in the opposite order.
        if ($discountPercent > 100 && $discountAmount >= 0 && $discountAmount <= 100) {
            [$discountPercent, $discountAmount] = [$discountAmount, $discountPercent];
        } elseif ($discountPercent > 100) {
            $discountAmount = max($discountAmount, $discountPercent);
            $discountPercent = $lineAmount > 0
                ? round(($discountAmount / $lineAmount) * 100, 2)
                : 0;
        }

        $discountPercent = min(100, max(0, $discountPercent));

        if (trim((string) ($mapped['activation_date'] ?? '')) !== '') {
            $activationDate = $this->parsePurchaseImportDate($mapped['activation_date']);
        }

        if ($taxableAmount <= 0) {
            $taxableAmount = max(0, $lineAmount - $discountAmount);
        }

        $gstTotal = $cgstAmount + $sgstAmount + $igstAmount;
        if ($netAmount <= 0) {
            $netAmount = $taxableAmount + $gstTotal;
        }

        if ($itemCode === '' && $itemName === '' && $qty <= 0 && $freeQty <= 0 && $rate <= 0 && $lineAmount <= 0) {
            return false;
        }

        $product = null;
        if ($itemCode !== '') {
            $query = Product::query()->where('SKU', $itemCode);
            if (Schema::hasColumn('products', 'barcode')) {
                $query->orWhere('barcode', $itemCode);
            }
            if (Schema::hasColumn('products', 'product_code')) {
                $query->orWhere('product_code', $itemCode);
            }
            $product = $query->first();
        }

        if (! $product && $itemName !== '') {
            $product = Product::where('name', $itemName)->first();
        }

        if (! $product) {
            $createData = [
                'name' => $itemName ?: ($itemCode ?: null),
                'SKU' => $itemCode ?: null,
                'price' => $rate ?: null,
                'cost_price' => $rate ?: null,
                'quantity' => $qty + $freeQty,
                'vendor_id' => $supplier->id,
                'branch_id' => $branchId,
                'create_by' => $authUser->id,
                'availablility' => 'in_stock',
                'status' => 'active',
            ];

            if (Schema::hasColumn('products', 'product_code') && $itemCode !== '') {
                $createData['product_code'] = $itemCode;
            }

            $product = Product::create($createData);
        } elseif (($qty + $freeQty) > 0) {
            $product->quantity = (float) ($product->quantity ?? 0) + $qty + $freeQty;
            if ($rate > 0) {
                $product->cost_price = $rate;
            }
            $product->save();
        }

        $gstDetails = array_values(array_filter([
            $cgstAmount > 0 ? ['name' => 'CGST', 'amount' => $cgstAmount] : null,
            $sgstAmount > 0 ? ['name' => 'SGST', 'amount' => $sgstAmount] : null,
            $igstAmount > 0 ? ['name' => 'IGST', 'amount' => $igstAmount] : null,
        ]));

        Purchases::create([
            'invoice_id' => $purchaseInvoice->id,
            'item' => $product->id ?? null,
            'quantity' => $qty ?: 0,
            'free_quantity' => $freeQty,
            'imei_no' => $imeiNo,
            'activation_date' => $activationDate?->toDateString(),
            'price' => $rate ?: 0,
            'amount_total' => $netAmount ?: ($lineAmount ?: (($rate ?: 0) * ($qty ?: 0))),
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'taxable_amount' => $taxableAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'net_amount' => $netAmount,
            'product_gst_details' => $gstDetails ?: null,
            'product_gst_total' => $gstTotal,
            'vendor_id' => $supplier->id,
            'purchase_status' => $purchaseStatus,
            'payment_status' => $paymentStatus,
            'branch_id' => $branchId,
            'created_by' => $authUser->id,
            'created_at' => $docDate->copy()->setTime(0, 0, 0),
            'updated_at' => now('Asia/Kolkata'),
        ]);

        return true;
    }

    private function refreshPurchaseImportInvoiceProducts(array $invoiceIds): void
    {
        $invoiceIds = array_values(array_unique(array_filter($invoiceIds)));

        foreach (array_chunk($invoiceIds, 100) as $chunk) {
            $purchasesByInvoice = Purchases::whereIn('invoice_id', $chunk)
                ->get()
                ->groupBy('invoice_id');

            foreach ($purchasesByInvoice as $invoiceId => $purchases) {
                $purchaseProducts = $purchases
                    ->map(function (Purchases $purchase) {
                        return [
                            'product_id' => $purchase->item,
                            'price' => (float) $purchase->price,
                            'quantity' => (float) $purchase->quantity,
                            'free_quantity' => (float) ($purchase->free_quantity ?? 0),
                            'imei_no' => $purchase->imei_no,
                            'activation_date' => $purchase->activation_date?->format('Y-m-d'),
                            'discount_percent' => (float) ($purchase->discount_percent ?? 0),
                            'discount_amount' => (float) ($purchase->discount_amount ?? 0),
                            'taxable_amount' => (float) ($purchase->taxable_amount ?? 0),
                            'cgst_amount' => (float) ($purchase->cgst_amount ?? 0),
                            'sgst_amount' => (float) ($purchase->sgst_amount ?? 0),
                            'igst_amount' => (float) ($purchase->igst_amount ?? 0),
                            'total' => (float) $purchase->amount_total,
                        ];
                    })
                    ->values()
                    ->all();

                PurchaseInvoice::where('id', $invoiceId)->update([
                    'products' => json_encode($purchaseProducts),
                    'updated_at' => now('Asia/Kolkata'),
                ]);
            }
        }
    }

    private function upsertPurchaseImportSupplier(string $supplierName, int $branchId, User $authUser): array
    {
        $supplier = User::where('role', 'vendor')
            ->where('branch_id', $branchId)
            ->where('name', $supplierName)
            ->first();

        $created = false;

        if (! $supplier) {
            $supplier = new User([
                'role' => 'vendor',
                'branch_id' => $branchId,
                'created_by' => $authUser->id,
                'status' => 1,
            ]);
            $created = true;
        }

        $supplier->name = $supplierName;
        $wasDirty = $supplier->isDirty();
        $supplier->save();

        UserDetail::firstOrCreate(
            ['user_id' => $supplier->id],
            [
                'address' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'pin_code' => '',
            ]
        );

        return [
            'supplier' => $supplier,
            'created' => $created,
            'updated' => ! $created && $wasDirty,
        ];
    }

    private function resolvePurchaseImportBranchId(User $authUser, $selectedSubAdminId = null): int
    {
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            return (int) $authUser->branch_id;
        }

        if ($authUser->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        if ($authUser->role === 'sub-admin') {
            return (int) $authUser->id;
        }

        return (int) ($authUser->branch_id ?: $authUser->id);
    }

    private function normalizePurchaseImportHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        $header = trim((string) $header, '_');

        return match ($header) {
            'sn', 's_no', 'sr_no', 'serial_no' => 'sn',
            'supplier', 'vendor', 'party_name', 'supplier_name', 'vendor_name' => 'supplier',
            'doc_no', 'doc_number', 'document_no', 'document_number' => 'doc_no',
            'doc_date', 'docdate', 'document_date' => 'doc_date',
            'ref_doc_no', 'ref_doc', 'ref_document_no', 'ref_document_number' => 'ref_doc_no',
            'ref_doc_date', 'ref_document_date' => 'ref_doc_date',
            'doc_value', 'docvalue', 'document_value', 'total' => 'doc_value',
            'status', 'doc_status', 'document_status' => 'status',
            'purchase_type', 'purch_type', 'type_of_purchase' => 'purchase_type',
            'item_model_code', 'item_code', 'model_code', 'product_code', 'sku', 'i_m_code', 'im_code' => 'item_model_code',
            'item_model', 'item', 'product', 'model', 'item_model_name' => 'item_model',
            'imei_no', 'imei', 'imei_number', 'serial_number' => 'imei_no',
            'activation_date', 'activation_dt', 'activation', 'ativation_date', 'ativation_dt' => 'activation_date',
            'qty', 'quantity' => 'qty',
            'free_qty', 'free_quantity', 'freeqty' => 'free_qty',
            'rate', 'rate_unit', 'price_per_unit', 'unit_price' => 'rate',
            'amount', 'line_amount', 'line_total', 'item_amount' => 'line_amount',
            'disc', 'discount' => 'discount',
            'disc_percent', 'discount_percent', 'disc_percentage', 'discount_percentage' => 'discount_percent',
            'disc_amount', 'discount_amount', 'discount_amt' => 'discount_amount',
            'taxable_amount', 'taxable_amt', 'taxable_value' => 'taxable_amount',
            'cgst_amount', 'cgst_amt', 'cgst' => 'cgst_amount',
            'sgst_amount', 'sgst_amt', 'sgst' => 'sgst_amount',
            'igst_amount', 'igst_amt', 'igst' => 'igst_amount',
            'net_amount', 'net_amt', 'net_value' => 'net_amount',
            default => $header,
        };
    }

    private function mapPurchaseImportRow(array $row, array $headers): array
    {
        $mapped = [];

        foreach ($headers as $column => $field) {
            $mapped[$field] = $row[$column] ?? null;
        }

        return $mapped;
    }

    private function isEmptyPurchaseImportRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parsePurchaseImportDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->timezone('Asia/Kolkata');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'm-d-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Kolkata');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value, 'Asia/Kolkata');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parsePurchaseImportAmount($value): float
    {
        $value = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return round((float) ($value !== '' ? $value : 0), 2);
    }

    private function parsePurchaseImportInteger($value): ?int
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        return $value !== '' ? (int) $value : null;
    }

    private function mapPurchaseImportStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'closed', 'complete', 'completed', 'paid' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending',
        };
    }

    private function generatePurchaseImportInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'INV-' . mt_rand(10000000, 99999999);
        } while (PurchaseInvoice::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    /**
     * Check if bill number is unique (excluding current invoice)
     */

    public function checkBillNoUnique(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_no' => 'required|string|max:255',
            'invoice_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'isUnique' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $authUser = auth()->guard('api')->user();

        if (!$authUser) {
            return response()->json([
                'isUnique' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $billNo = $request->bill_no;
        $invoiceId = $request->invoice_id;
        $subAdminId = $request->selectedSubAdminId ?? session('selectedSubAdminId');

        // Determine branch_id
        if ($authUser->role === 'sub-admin') {
            $branchId = $authUser->id;
        } elseif ($authUser->role === 'admin' && !empty($subAdminId)) {
            $branchId = $subAdminId;
        } elseif ($authUser->role === 'staff') {
            $branchId = $authUser->branch_id;
        } else {
            $branchId = $authUser->id;
        }

        // 🔥 UNIQUE CHECK
        $query = PurchaseInvoice::where('bill_no', $billNo)
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0);

        if ($invoiceId) {
            $query->where('id', '!=', $invoiceId);
        }

        $exists = $query->exists();

        if ($exists) {
            return response()->json([
                'isUnique' => false,
                'message' => 'This bill number already exists.'
            ], 200);
        }

        return response()->json([
            'isUnique' => true,
            'message' => 'Bill number is available.'
        ], 200);
    }
}
