<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\PendingEmiService;
use App\Services\StaffDepartmentScope;
use App\Models\BankMaster;
use App\Models\Category;
use App\Models\LabourItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentStore;
use App\Models\Sales_Labour_Items;
use App\Models\Product;
use App\Models\SalesReturn;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    private function fallbackSetting(?int $branchId = null): Setting
    {
        return Setting::where('branch_id', $branchId)->first()
            ?? Setting::first()
            ?? new Setting([
                'name' => 'ERP Inventory System',
                'email' => 'info@gmail.com',
                'phone' => 1234567890,
                'address' => 'Adajan Surat',
                'logo' => 'admin/assets/img/logo-image.jpg',
                'currency_symbol' => '₹',
                'currency_position' => 'left',
            ]);
    }

    private function attachOrderCustomerDetails(Order $order): void
    {
        $orderUser = $order->relationLoaded('user')
            ? $order->user
            : ($order->user_id ? User::with('userDetail')->find($order->user_id) : null);

        if ($orderUser) {
            $order->customer_name       = $orderUser->name ?? 'Walk-in Customer';
            $order->customer_email      = $orderUser->email ?? '';
            $order->customer_phone      = $orderUser->phone ?? '';
            $order->customer_address    = optional($orderUser->userDetail)->address ?? '';
            $order->customer_city       = optional($orderUser->userDetail)->city ?? '';
            $order->customer_country    = optional($orderUser->userDetail)->country ?? '';
        } else {
            $order->customer_name       = $order->customer_name ?? 'Walk-in Customer';
            $order->customer_email      = $order->customer_email ?? '';
            $order->customer_phone      = $order->customer_phone ?? '';
            $order->customer_address    = $order->customer_address ?? '';
            $order->customer_city       = $order->customer_city ?? '';
            $order->customer_country    = $order->customer_country ?? '';
        }
    }

    public function sales_list(Request $request)
    {
        $user = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        $years = Order::where('isDeleted', 0)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $banks = BankMaster::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $setting = $this->fallbackSetting($branchIdToUse);
        $financialYearEnabled = (bool) ($setting->financial_year ?? true);

        // Staff list for staff-wise filter and assignment
        $staffList = User::where('role', 'staff')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('sales/saleslist', compact('years', 'banks', 'financialYearEnabled', 'staffList'));
    }

    public function import_sales(Request $request)
    {
        return view('sales/importsalesinvoice');
    }

    public function add_sales(Request $request)
    {

        return view('sales/add-sales');
    }
    public function edit_sales($id)
    {
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        // 🔹 Load sales record - IMPORTANT: Load product_gst_details from order_items
        $sales = Order::with(['order_items' => function ($query) {
            $query->select(
                'id',
                'order_id',
                'product_id',
                'product_gst_details',
                'product_gst_total',
                'quantity',
                'price',
                'discount_percentage',
                'discount_amount',
                'total_amount',
                'imei_no'
            );
        }, 'order_items.product', 'payments.bank'])->find($id);

        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Sales record not found.');
        }

        $latestEmiPayment = $sales->payments
            ->filter(function ($payment) {
                return (int) ($payment->isDeleted ?? 0) === 0
                    && strtolower((string) ($payment->payment_method ?? '')) === 'emi';
            })
            ->sortByDesc('id')
            ->first();

        if ($latestEmiPayment) {
            $sales->setAttribute('emi_down_payment', $sales->emi_down_payment ?? $latestEmiPayment->emi_down_payment);
            $sales->setAttribute('emi_loan_amount', $sales->emi_loan_amount ?? $latestEmiPayment->emi_loan_amount);
            $sales->setAttribute('emi_interest_rate', $sales->emi_interest_rate ?? $latestEmiPayment->emi_interest_rate);
            $sales->setAttribute('emi_tenure', $sales->emi_tenure ?? $latestEmiPayment->emi_tenure);
            $sales->setAttribute('emi_monthly_amount', $sales->emi_monthly_amount ?? $latestEmiPayment->emi_monthly_amount);
            $sales->setAttribute('emi_aadhar_number', $sales->emi_aadhar_number ?? $latestEmiPayment->emi_aadhar_number);
            $sales->setAttribute('emi_pan_number', $sales->emi_pan_number ?? $latestEmiPayment->emi_pan_number);
            $sales->setAttribute('emi_guarantor_name', $sales->emi_guarantor_name ?? $latestEmiPayment->emi_guarantor_name);
        }

        $update_id = $id;

        // 🔹 Load related data
        $usernamesQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0);
        StaffDepartmentScope::applyStaffCreatedByScope($usernamesQuery, $user);
        $usernames = $usernamesQuery->get();

        $category = Category::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $TaxRate = TaxRate::where('status', 'active')->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        $products = Product::where('status', 'active')
            ->where('availablility', 'in_stock')
            ->where('branch_id', $branchIdToUse)
            ->get();

        $setting = $this->fallbackSetting($branchIdToUse);
        $labourItems = LabourItem::where('created_by', $branchIdToUse)
            ->where('isDeleted', false)
            ->get();
        $banks = BankMaster::where('branch_id', $branchIdToUse)
            ->where('status', 1)
            ->where('isDeleted', 0)
            ->get();

        // Staff list for assignment dropdown (all users)
        $staffList = User::where('role', 'staff')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);
        $userRole = $user->role ?? '';

        return view('sales/edit-sales', compact('sales', 'TaxRate', 'category', 'usernames', 'products', 'update_id', 'setting', 'labourItems', 'banks', 'staffList', 'userRole'));
    }

    public function sales_details($id)
    {
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }

        // 🔹 Get branch-specific setting
        $setting          = $this->fallbackSetting($branchIdToUse);
        $currencySymbol   = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        // 🔹 Load sales with order items + products
        $sales = Order::with(['order_items.product'])->find($id);

        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }
        $totalPaid = PaymentStore::where('order_id', $id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        // ✅ Final payable amount (IMPORTANT)
        $finalAmount =
            ($sales->total_amount ?? 0)
            + ($sales->total_gst ?? 0)
            - ($sales->discount_amount ?? 0);
        // dd($finalAmount);
        // ✅ Pending & Extra calculation
        $pendingAmount = max(0, $finalAmount - $totalPaid);
        $extraPaid     = max(0, $totalPaid - $finalAmount);
        // dd($pendingAmount);
        // dd($extraPaid);
        // ✅ Attach values for Blade
        $sales->final_amount   = $finalAmount;
        $sales->total_paid     = $totalPaid;
        $sales->pending_amount = $pendingAmount;
        $sales->extra_paid     = $extraPaid;

        // dd($totalPaid);
        // dd($extraPaid);

        // 🔹 Company info (branch-specific setting)
        $compenyinfo = $setting;

        // 🔹 Get taxes (safely handle null/empty tax_id)
        // $taxIds = ! empty($sales->tax_id) ? json_decode($sales->tax_id, true) : [];

        // $taxes  = ! empty($taxIds)
        //     ? TaxRate::where('branch_id', $branchIdToUse)
        //     ->whereIn('id', $taxIds)
        //     ->where('isDeleted', 0)
        //     ->get()
        //     : collect();

        // 🔹 Order items & totals
        $orderItems  = OrderItem::where('order_id', $id)->get();
        $totalAmount = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });


        $view_id = $id; // define view_id for blade
        $user    = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;
        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;
        $userDeliveryAddress = $user && $user->userDetail ? $user->userDetail->delivery_address : null;

        if ($user) {
            $sales->customer_role       = ucfirst($user->role ?? 'Customer');
            $sales->customer_name       = $user->name ?? 'Walk-in Customer';
            $sales->customer_email      = $user->email ?? '';
            $sales->customer_phone      = $user->phone ?? '';
            $sales->customer_address    = optional($user->userDetail)->address ?? '';
            $sales->customer_city       = optional($user->userDetail)->city ?? '';
            $sales->customer_country    = optional($user->userDetail)->country ?? '';
            $sales->customer_gst_number = $user->gst_number ?? '';
            $sales->customer_pan_number = $user->pan_number ?? '';
        } else {
            // default values if no user is linked
            $sales->customer_role       = 'Customer';
            $sales->customer_name       = 'Walk-in Customer';
            $sales->customer_email      = '';
            $sales->customer_phone      = '';
            $sales->customer_address    = '';
            $sales->customer_city       = '';
            $sales->customer_country    = '';
            $sales->customer_gst_number = '';
            $sales->customer_pan_number = '';
        }

        // ✅ Check if payment already started for this order
        $hasPaymentStarted = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->exists();

        // ✅ Check if return already started for this order
        $hasReturnStarted = SalesReturn::where('order_id', $view_id)->exists();

        $pageTitle = match ($sales->quotation_status ?? 'sales') {
            'quotation'        => 'Sale Quotation Details',
            'advance_receipt'  => 'Advance Receipt Details',
            default            => 'Sale Details',
        };

        return view('sales.sales-details', compact(
            'view_id',
            'sales',
            'totalAmount',
            'compenyinfo',
            'setting',
            'userAddress',
            'userDeliveryAddress',
            'orderItems',
            'currencySymbol',
            'currencyPosition',
            'hasPaymentStarted',
            'hasReturnStarted',
            'pageTitle'
        ));
    }

    public function salse_invoice($id)
    {
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($user->role === 'staff' && $user->branch_id) {
            $branchIdToUse = $user->branch_id;
        } elseif ($user->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $user->id;
        }
        $view_id = $id;
        $sales   = Order::find($view_id);
        $setting = $this->fallbackSetting($branchIdToUse); // Get currency info
        // dd($setting);
        if (! $sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }
        $totalPaid = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        $extraPaid = max(0, $totalPaid - ($sales->total_amount ?? 0));

        $sales->total_paid = $totalPaid;
        $sales->extra_paid = $extraPaid;
        // $taxIds      = json_decode($sales->tax_id, true);
        // $taxes       = TaxRate::where('branch_id', $branchIdToUse)->whereIn('id', $taxIds)->where('isDeleted', 0)->get();
        $orderItems  = OrderItem::where('order_id', $view_id)->get();
        $totalAmount = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;
        $userDeliveryAddress = $user && $user->userDetail ? $user->userDetail->delivery_address : null;

        // ✅ Check if payment already started for this order
        $hasPaymentStarted = PaymentStore::where('order_id', $view_id)
            ->where('isDeleted', 0)
            ->exists();

        // ✅ Check if return already started for this order
        $hasReturnStarted = SalesReturn::where('order_id', $view_id)->exists();

        $pageTitle = match ($sales->quotation_status ?? 'sales') {
            'quotation'        => 'Sale Quotation Invoice',
            'advance_receipt'  => 'Advance Receipt Invoice',
            default            => 'Sale Invoice',
        };

        return view('sales/salse-invoice', compact('view_id', 'sales', 'totalAmount', 'setting', 'userAddress', 'userDeliveryAddress', 'orderItems', 'hasPaymentStarted', 'hasReturnStarted', 'pageTitle'));
    }

    // Show delivery page for a sale
    public function delivery($id)
    {
        $order = Order::with('order_items.product')->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        // Compute delivered qty per order item
        $orderItems = $order->order_items->map(function ($item) {
            $delivered = Delivery::where('order_item_id', $item->id)->where('order_id', $item->order_id)->sum('delivered_quantity');
            $item->delivered_quantity = (float) $delivered;
            $item->remaining_to_deliver = max(0, (float) $item->quantity - (float) $delivered);
            return $item;
        });

        // previous deliveries for this order
        $previousDeliveries = Delivery::where('order_id', $order->id)->with(['orderItem', 'product'])->get();

        // branch/company settings (fallback)
        $user       = auth()->user();
        $subAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id ? $user->branch_id : ($user->role === 'admin' && ! empty($subAdminId) ? $subAdminId : $user->id);
        $setting = $this->fallbackSetting($branchIdToUse);

        // Attach customer details to $order (so view mirrors sales details)
        $orderUser = $order->user_id ? User::with('userDetail')->find($order->user_id) : null;
        if ($orderUser) {
            $order->customer_role       = ucfirst($orderUser->role ?? 'Customer');
            $order->customer_name       = $orderUser->name ?? 'Walk-in Customer';
            $order->customer_email      = $orderUser->email ?? '';
            $order->customer_phone      = $orderUser->phone ?? '';
            $order->customer_address    = optional($orderUser->userDetail)->address ?? '';
            $order->customer_city       = optional($orderUser->userDetail)->city ?? '';
            $order->customer_country    = optional($orderUser->userDetail)->country ?? '';
            $order->customer_gst_number = $orderUser->gst_number ?? '';
            $order->customer_pan_number = $orderUser->pan_number ?? '';
        } else {
            $order->customer_role       = 'Customer';
            $order->customer_name       = $order->customer_name ?? 'Walk-in Customer';
            $order->customer_email      = $order->customer_email ?? '';
            $order->customer_phone      = $order->customer_phone ?? '';
            $order->customer_address    = $order->customer_address ?? '';
            $order->customer_city       = $order->customer_city ?? '';
            $order->customer_country    = $order->customer_country ?? '';
            $order->customer_gst_number = $order->customer_gst_number ?? '';
            $order->customer_pan_number = $order->customer_pan_number ?? '';
        }

        return view('sales.delivery', compact('order', 'orderItems', 'previousDeliveries', 'setting'));
    }

    // Store delivery entries
    public function storeDelivery(Request $request)
    {
        Log::info('storeDelivery called', ['request' => $request->all()]);
        try {
            $data = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'items' => 'required|array',
                'items.*.order_item_id' => 'required|integer|exists:order_items,id',
                'items.*.delivered_quantity' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('storeDelivery validation failed', ['errors' => $e->errors(), 'request' => $request->all()]);
            throw $e; // rethrow so Laravel handles the redirect/back with errors as usual
        }

        $order = Order::find($data['order_id']);
        if (! $order) {
            return back()->with('error', 'Order not found.');
        }

        DB::beginTransaction();
        try {
            $totalDelivered = 0;
            $createdDeliveryIds = [];
            foreach ($data['items'] as $it) {
                $orderItem = OrderItem::find($it['order_item_id']);
                if (! $orderItem) continue;

                $alreadyDelivered = Delivery::where('order_item_id', $orderItem->id)->where('order_id', $order->id)->sum('delivered_quantity');
                $remaining = max(0, $orderItem->quantity - $alreadyDelivered);
                $toDeliver = (float) $it['delivered_quantity'];

                if ($toDeliver <= 0) continue;
                if ($toDeliver > $remaining) {
                    Log::warning('Attempt to over-deliver', ['order_id' => $order->id, 'order_item_id' => $orderItem->id, 'toDeliver' => $toDeliver, 'remaining' => $remaining]);
                    DB::rollBack();
                    return back()->with('error', "Delivered quantity for product {$orderItem->product_id} cannot exceed remaining quantity.");
                }

                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'delivered_quantity' => $toDeliver,
                    'ordered_quantity' => $orderItem->quantity,
                    'status' => ($toDeliver === $orderItem->quantity) ? 'delivered' : 'partially_delivered',
                    'delivered_by' => auth()->id(),
                    'delivered_at' => now(),
                ]);
                if ($delivery && $delivery->id) {
                    $createdDeliveryIds[] = $delivery->id;
                    Log::info('Delivery created', ['delivery_id' => $delivery->id, 'order_id' => $order->id, 'order_item_id' => $orderItem->id, 'qty' => $toDeliver]);
                } else {
                    Log::warning('Delivery::create returned null or no id', ['order_id' => $order->id, 'order_item_id' => $orderItem->id]);
                }
                $totalDelivered += $toDeliver;
            }

            // Update order delivery_status: delivered / partial / pending
            $allOrdered = $order->order_items->sum('quantity');
            $allDelivered = Delivery::where('order_id', $order->id)->sum('delivered_quantity');
            if ($allDelivered >= $allOrdered) {
                $order->delivery_status = 'delivered';
            } elseif ($allDelivered > 0) {
                $order->delivery_status = 'partial';
            } else {
                $order->delivery_status = 'pending';
            }
            $order->save();

            DB::commit();
            Log::info('storeDelivery completed and committed', ['order_id' => $order->id, 'totalDelivered' => $totalDelivered, 'created' => $createdDeliveryIds]);
            $query = [];
            if (! empty($createdDeliveryIds)) {
                $query['delivery_ids'] = implode(',', $createdDeliveryIds);
            }
            $url = route('sales.delivery.challan.pdf', ['id' => $order->id]);
            if (! empty($query)) {
                $url .= '?' . http_build_query($query);
            }
            return redirect($url)->with('success', 'Delivery recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeDelivery exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error saving delivery: ' . $e->getMessage());
        }
    }

    public function getPendingEmis(Request $request)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchId = ! empty($selectedSubAdminId)
            ? (int) $selectedSubAdminId
            : ($user->role === 'staff' && $user->branch_id ? (int) $user->branch_id : (int) $user->id);

        [$year, $month] = PendingEmiService::parseMonth($request->input('month'));
        $pendingEmis = PendingEmiService::getPendingEmis($branchId, $year, $month);
        $settings = Setting::where('branch_id', $branchId)->first();

        return response()->json([
            'status' => true,
            'month' => sprintf('%04d-%02d', $year, $month),
            'count' => count($pendingEmis),
            'currency_symbol' => $settings->currency_symbol ?? '₹',
            'currency_position' => $settings->currency_position ?? 'left',
            'data' => $pendingEmis,
        ]);
    }

    public function updateDeliveryStatus(Request $request, Delivery $delivery)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id
            ? $user->branch_id
            : (! empty($selectedSubAdminId) ? $selectedSubAdminId : $user->id);

        $delivery->load('order');
        if (! $delivery->order || (int) $delivery->order->branch_id !== (int) $branchIdToUse) {
            abort(404);
        }

        $allowedStatuses = [
            'pending',
            'delivered',
            'partially_delivered',
            'cancelled',
        ];

        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);

        $delivery->status = $data['status'];
        $delivery->save();

        $delivery->order->delivery_status = $data['status'] === 'partially_delivered'
            ? 'partial'
            : $data['status'];
        $delivery->order->save();

        return response()->json([
            'status' => true,
            'message' => 'Delivery status updated successfully.',
            'data' => [
                'id' => $delivery->id,
                'status' => $delivery->status,
            ],
        ]);
    }

    public function updateOrderDeliveryStatus(Request $request, Order $order)
    {
        $user = auth()->user();
        $selectedSubAdminId = session('selectedSubAdminId');
        $branchIdToUse = $user->role === 'staff' && $user->branch_id
            ? $user->branch_id
            : (! empty($selectedSubAdminId) ? $selectedSubAdminId : $user->id);

        if ((int) $order->branch_id !== (int) $branchIdToUse || (int) $order->isDeleted === 1) {
            abort(404);
        }

        $allowedStatuses = [
            'pending',
            'delivered',
            'partially_delivered',
            'cancelled',
        ];

        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);

        $order->delivery_status = $data['status'] === 'partially_delivered'
            ? 'partial'
            : $data['status'];
        $order->save();

        $latestDelivery = Delivery::where('order_id', $order->id)
            ->latest('created_at')
            ->first();

        if ($latestDelivery) {
            $latestDelivery->status = $data['status'];
            $latestDelivery->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Order delivery status updated successfully.',
            'data' => [
                'id' => $order->id,
                'status' => $data['status'],
            ],
        ]);
    }

    // public function salse_invoice_pdf($id)
    // {
    //     $view_id    = $id;
    //     $sales      = Order::find($view_id);
    //     $user       = Auth::user();
    //     $subAdminId = session('selectedSubAdminId') ?? $user->id;

    //     if ($user->role === 'staff' && $user->branch_id) {
    //         $setting = Setting::where('branch_id', $user->branch_id)->first();
    //     } else {
    //         $setting = Setting::where('branch_id', $subAdminId)->first();
    //     }

    //     if (! $sales) {
    //         return redirect()->route('sales.list')->with('error', 'Order not found.');
    //     }
    //     $labourItems = Sales_Labour_Items::where('order_id', $id)
    //         ->with('labourItem')
    //         ->get();
    //         // dd($labourItems);
    //     $labourCost = 0;
    //     if ($labourItems && $labourItems->isNotEmpty()) {
    //         foreach ($labourItems as $labourItem) {
    //             $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
    //         }
    //     }

    //     // Fetch user data (assuming 'user_id' in orders table)
    //     $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

    //     // Helper function for currency formatting
    //     $formatCurrency = function ($amount) use ($setting) {
    //         $amount = number_format($amount, 2);
    //         return $setting->currency_position === 'right'
    //             ? $amount . $setting->currency_symbol
    //             : $setting->currency_symbol . $amount;
    //     };

    //     // ✅ Subtotal (Amount before GST)
    //     $orderItems = OrderItem::where('order_id', $view_id)->get();
    //     $subtotal   = $orderItems->sum(function ($item) {
    //         return $item->price * $item->quantity;
    //     });

    //     // dd($orderItems);

    //     // ✅ Discount
    //     $discountPercent = $sales->discount ?? 0;
    //     $discountAmount  = ($discountPercent / 100) * $subtotal;
    //     $afterDiscount   = $subtotal - $discountAmount;

    //     // ✅ Tax calculation only if gst_option = 'with'
    //     // $taxDetails = [];
    //     // if ($sales->gst_option === 'with_gst') {
    //     //     $taxIds = json_decode($sales->tax_id, true) ?? [];
    //     //     if (! empty($taxIds)) {
    //     //         $taxes = TaxRate::whereIn('id', $taxIds)->get();
    //     //         foreach ($taxes as $tax) {
    //     //             $taxAmount    = ($tax->tax_rate / 100) * $afterDiscount;
    //     //             $taxDetails[] = [
    //     //                 'name'             => $tax->tax_name,
    //     //                 'rate'             => $tax->tax_rate,
    //     //                 'amount'           => $taxAmount,
    //     //                 'formatted_amount' => $formatCurrency($taxAmount),
    //     //             ];
    //     //         }
    //     //     }
    //     // }
    //     $totalGstAmount = 0;
    //     $taxSummary = [];

    //     foreach ($orderItems as $item) {
    //         $totalGstAmount += (float) ($item->product_gst_total ?? 0);
    //         $gstDetails = $item->product_gst_details;

    //         // Handle legacy/double-encoded JSON payloads.
    //         if (is_string($gstDetails)) {
    //             $gstDetails = json_decode($gstDetails, true);
    //             if (is_string($gstDetails)) {
    //                 $gstDetails = json_decode($gstDetails, true);
    //             }
    //         }

    //         if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
    //             $gstDetails = [$gstDetails];
    //         }

    //         if (!empty($gstDetails) && is_array($gstDetails)) {
    //             foreach ($gstDetails as $tax) {
    //                 if (!is_array($tax)) {
    //                     continue;
    //                 }

    //                 $taxName = $tax['tax_name'] ?? 'GST';
    //                 $taxRate = $tax['tax_rate'] ?? 0;
    //                 $taxAmount = (float) ($tax['tax_amount'] ?? 0);
    //                 $key = $taxName . '_' . $taxRate;

    //                 if (!isset($taxSummary[$key])) {
    //                     $taxSummary[$key] = [
    //                         'name'   => $taxName,
    //                         'rate'   => $taxRate,
    //                         'amount' => 0,
    //                     ];
    //                 }

    //                 $taxSummary[$key]['amount'] += $taxAmount;
    //             }
    //         }
    //     }

    //     // format GST summary
    //     $taxDetails = [];
    //     foreach ($taxSummary as $tax) {
    //         $taxDetails[] = [
    //             'name'             => $tax['name'],
    //             'rate'             => $tax['rate'],
    //             'amount'           => $tax['amount'],
    //             'formatted_amount' => $formatCurrency($tax['amount']),
    //         ];
    //     }

    //     // final total
    //     $finalTotal = $afterDiscount + $totalGstAmount;


    //     // ✅ Final total
    //     $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

    //     // ✅ Prepare formatted values
    //     $formattedSubtotal       = $formatCurrency($subtotal);
    //     $formattedDiscountAmount = $formatCurrency($discountAmount);
    //     $formattedAfterDiscount  = $formatCurrency($afterDiscount);
    //     $formattedFinalTotal     = $formatCurrency($finalTotal);

    //     // ✅ Retrieve customer data
    //     $customer = $user ? [
    //         'name'       => $user->name ?? 'walk-in-customer',
    //         'email'      => $user->email ?? '',
    //         'phone'      => $user->phone ?? '',
    //         'address'    => optional($user->userDetail)->address ?? '',
    //         'gst_number' => $user->gst_number ?? '',
    //         'pan_number' => $user->pan_number ?? '',
    //     ] : [
    //         'name'       => 'walk-in-customer',
    //         'email'      => '',
    //         'phone'      => '',
    //         'address'    => '',
    //         'gst_number' => '',
    //         'pan_number' => '',
    //     ];

    //     $paidAmount = PaymentStore::where('order_id', $sales->id)
    //         ->where('isDeleted', 0)
    //         ->sum('payment_amount');

    //     // ✅ Fetch returns
    //     $returns = \App\Models\SalesReturn::with('items.product')
    //         ->where('order_id', $view_id)
    //         ->get();

    //     // ✅ Pending amount (single source of truth)
    //     $pendingAmount = $sales->remaining_amount ?? 0;

    //     // ✅ Extra Paid calculation
    //         $totalOrderAmount = $sales->total_amount ?? $finalTotal; // fallback safety
    //         $extraPaid = max(0, $paidAmount - $totalOrderAmount);

    //     // ✅ Prepare data for view
    //     $pdfData = [
    //         'view_id'                => $view_id,
    //         'sales'                  => $sales,
    //         'setting'                => $setting,
    //         'orderItems'             => $orderItems,
    //         'salesItems'             => $orderItems,
    //         'labourItems'            => $labourItems,
    //         'returns'                => $returns,
    //         'taxDetails1'            => $taxDetails,
    //         'totalGst'      => $formatCurrency($totalGstAmount),
    //         'finalTotal'    => $formatCurrency($finalTotal),
    //         'customer'               => [
    //             'name'    => $user->name ?? 'walk-in-customer',
    //             'email'   => $user->email ?? '',
    //             'phone'   => $user->phone ?? '',
    //             'pan_number'   => $user->pan_number ?? '',
    //             'gst_number'   => $user->gst_number ?? '',
    //             'address' => optional($user->userDetail)->address ?? 'arga',
    //         ],
    //         'user'                   => $user ? $user->toArray() : null,
    //         'subtotal'               => $formattedSubtotal,
    //         'discountPercent'        => $discountPercent,
    //         'discountAmount'         => $formattedDiscountAmount,
    //         'afterDiscount'          => (float) $afterDiscount,  // numeric
    //         'formattedAfterDiscount' => $formattedAfterDiscount, // formatted
    //         'finalTotal'             => $formattedFinalTotal,
    //         'taxDetails1'            => $taxDetails,
    //         'paidAmount'             => $paidAmount,
    //         'pendingAmount'          => $pendingAmount,
    //         'extraPaid'              => $extraPaid,
    //     ];
    //     // dd($pdfData);

    //     // ✅ Load and render PDF
    //     // $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

    //     // return $pdf->stream('invoice_' . $view_id . '.pdf');
    //     // ===== Invoice size condition =====
    //         if ($setting && $setting->invoice_size === 'small') {

    //             $pdf = PDF::loadView('sales.salse-invoice-small-pdf', $pdfData)
    //                     ->setPaper('A5', 'portrait');

    //         } else {

    //             $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData)
    //                     ->setPaper('A4', 'portrait');
    //         }
    //         // ==================================

    //     return $pdf->stream('invoice_' . $view_id . '.pdf');
    // }
    public function salse_invoice_pdf($id)
    {
        $view_id    = $id;
        $sales      = Order::find($view_id);

        if (! $sales) {
            abort(404, 'Order not found.');
        }

        if (($sales->quotation_status ?? '') === 'quotation') {
            // abort(404, 'Invoice not available.'); // Allow quotation PDF download
        }

        $setting = $this->fallbackSetting($sales->branch_id ?? null);

        $labourItems = Sales_Labour_Items::where('order_id', $id)
            ->with('labourItem')
            ->get();

        $labourCost = 0;
        if ($labourItems && $labourItems->isNotEmpty()) {
            foreach ($labourItems as $labourItem) {
                $labourCost += ($labourItem->qty ?? 0) * ($labourItem->price ?? 0);
            }
        }

        // Fetch user data
        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        // Helper function for currency formatting
        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        // Subtotal
        $orderItems = OrderItem::where('order_id', $view_id)->get();
        $subtotal   = $orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount  = ($discountPercent / 100) * $subtotal;
        $afterDiscount   = $subtotal - $discountAmount;

        // Calculate GST
        $totalGstAmount = 0;
        $taxSummary = [];

        foreach ($orderItems as $item) {
            $totalGstAmount += (float) ($item->product_gst_total ?? 0);
            $gstDetails = $item->product_gst_details;

            if (is_string($gstDetails)) {
                $gstDetails = json_decode($gstDetails, true);
                if (is_string($gstDetails)) {
                    $gstDetails = json_decode($gstDetails, true);
                }
            }

            if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
                $gstDetails = [$gstDetails];
            }

            if (!empty($gstDetails) && is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    if (!is_array($tax)) {
                        continue;
                    }

                    $taxName = $tax['tax_name'] ?? 'GST';
                    $taxRate = $tax['tax_rate'] ?? 0;
                    $taxAmount = (float) ($tax['tax_amount'] ?? 0);
                    $key = $taxName . '_' . $taxRate;

                    if (!isset($taxSummary[$key])) {
                        $taxSummary[$key] = [
                            'name'   => $taxName,
                            'rate'   => $taxRate,
                            'amount' => 0,
                        ];
                    }

                    $taxSummary[$key]['amount'] += $taxAmount;
                }
            }
        }

        // Format GST summary
        $taxDetails = [];
        foreach ($taxSummary as $tax) {
            $taxDetails[] = [
                'name'             => $tax['name'],
                'rate'             => $tax['rate'],
                'amount'           => $tax['amount'],
                'formatted_amount' => $formatCurrency($tax['amount']),
            ];
        }

        // Calculate Return Amount
        $totalReturnAmount = 0;
        $returns = \App\Models\SalesReturn::with('items.product')
            ->where('order_id', $view_id)
            ->get();

        $allItemsFullyReturned = false;

        if ($returns->isNotEmpty()) {
            foreach ($returns as $ret) {
                $totalReturnAmount += (float) ($ret->total_amount ?? 0);
            }

            // Check if all items are fully returned
            $orderItemsQuantities = [];
            foreach ($orderItems as $item) {
                $orderItemsQuantities[$item->id] = $item->quantity;
            }

            $returnedQuantities = [];
            foreach ($returns as $ret) {
                foreach ($ret->items as $retItem) {
                    if (!isset($returnedQuantities[$retItem->order_item_id])) {
                        $returnedQuantities[$retItem->order_item_id] = 0;
                    }
                    $returnedQuantities[$retItem->order_item_id] += $retItem->quantity;
                }
            }

            $allItemsFullyReturned = true;
            foreach ($orderItemsQuantities as $orderItemId => $originalQty) {
                $returnedQty = $returnedQuantities[$orderItemId] ?? 0;
                if ($returnedQty < $originalQty) {
                    $allItemsFullyReturned = false;
                    break;
                }
            }
        }

        // Get shipping charge
        $shippingCharge = (float) ($sales->shipping ?? 0);

        // Calculate return amount with shipping if fully returned
        $totalReturnWithShipping = $totalReturnAmount;
        if ($allItemsFullyReturned && $totalReturnAmount > 0) {
            $totalReturnWithShipping = $totalReturnAmount + $shippingCharge;
        }

        // Final total
        $finalTotal = $afterDiscount + $totalGstAmount + $shippingCharge + $labourCost;

        $allPayments = PaymentStore::where('order_id', $sales->id)
            ->where('isDeleted', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $paidAmount = $allPayments->sum('payment_amount');

        $emiPayments = PaymentStore::where('order_id', $sales->id)
            ->where('isDeleted', 0)
            ->where(function ($query) {
                $query->where('payment_method', 'emi')
                    ->orWhere('payment_type', 'emi');
            })
            ->whereNotNull('emi_month')
            ->orderBy('emi_month')
            ->get();

        // Pending amount = Final Total - Total Returns - Paid Amount
        $pendingAmount = max(0, $finalTotal - $totalReturnWithShipping - $paidAmount);

        // Extra Paid calculation
        $extraPaid = max(0, $paidAmount - ($finalTotal - $totalReturnWithShipping));

        // Prepare formatted values
        $formattedSubtotal       = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount  = $formatCurrency($afterDiscount);
        $formattedTotalGstAmount = $formatCurrency($totalGstAmount);
        $formattedShippingCharge = $formatCurrency($shippingCharge);
        $formattedLabourCost     = $formatCurrency($labourCost);
        $formattedFinalTotal     = $formatCurrency($finalTotal);
        $formattedReturnAmount   = $formatCurrency($totalReturnWithShipping);
        $formattedPaidAmount     = $formatCurrency($paidAmount);
        $formattedPendingAmount  = $formatCurrency($pendingAmount);
        $formattedExtraPaid      = $formatCurrency($extraPaid);

        // Determine return status
        $returnStatus = 'No return';
        $returnStatusColor = '#28c76f';
        if ($totalReturnAmount > 0) {
            if ($totalReturnWithShipping >= $finalTotal) {
                $returnStatus = 'Fully Returned';
                $returnStatusColor = '#ea5455';
            } else {
                $returnStatus = 'Partially Returned';
                $returnStatusColor = '#ff9f43';
            }
        }

        // Prepare customer data
        $customer = $user ? [
            'name'       => $user->name ?? 'walk-in-customer',
            'company_name' =>$user->company_name ?? '',
            'email'      => $user->email ?? '',
            'phone'      => $user->phone ?? '',
            'address'    => optional($user->userDetail)->address ?? '',
            'delivery_address' => optional($user->userDetail)->delivery_address ?? '',
            'gst_number' => $user->gst_number ?? '',
            'pan_number' => $user->pan_number ?? '',
        ] : [
            'name'       => 'walk-in-customer',
                'company_name' => '',
            'email'      => '',
            'phone'      => '',
            'address'    => '',
            'delivery_address' => '',
            'gst_number' => '',
            'pan_number' => '',
        ];

        // dd($formattedPaidAmount);
        // Prepare data for view
        $pdfData = [
            'view_id'                => $view_id,
            'sales'                  => $sales,
            'setting'                => $setting,
            'orderItems'             => $orderItems,
            'salesItems'             => $orderItems,
            'labourItems'            => $labourItems,
            'returns'                => $returns,
            'taxDetails1'            => $taxDetails,
            'totalGst'               => $formattedTotalGstAmount,
            'finalTotal'             => $formattedFinalTotal,
            'subtotal'               => $formattedSubtotal,
            'discountPercent'        => $discountPercent,
            'discountAmount'         => $formattedDiscountAmount,
            'afterDiscount'          => (float) $afterDiscount,
            'formattedAfterDiscount' => $formattedAfterDiscount,
            'shippingCharge'         => $formattedShippingCharge,
            'labourCost'             => $formattedLabourCost,
            'returnAmount'           => $formattedReturnAmount,
            'returnStatus'           => $returnStatus,
            'returnStatusColor'      => $returnStatusColor,
            'totalReturnAmount'      => $totalReturnWithShipping,
            'allItemsFullyReturned'  => $allItemsFullyReturned,
            'customer'               => $customer,
            'user'                   => $user ? $user->toArray() : null,
            'paidAmount' => $paidAmount,
            'pendingAmount'          => $formattedPendingAmount,
            'extraPaid'              => $formattedExtraPaid,
            'pendingAmountNumeric'   => $pendingAmount,
            'emiPayments'            => $emiPayments,
            'allPayments'            => $allPayments,
        ];

        // Load and render PDF
        if ($setting && $setting->invoice_size === 'small') {
            $pdf = PDF::loadView('sales.salse-invoice-small-pdf', $pdfData)
                ->setPaper('A5', 'portrait');
        } else {
            $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData)
                ->setPaper('A4', 'portrait');
        }

        return $pdf->stream('invoice_' . $view_id . '.pdf');
    }

    // Show delivery challan (printable). If `delivery_ids` query param provided, show only those deliveries.
    public function deliveryChallan($id, Request $request)
    {
        $order = Order::with(['user.userDetail', 'order_items.product'])->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $this->attachOrderCustomerDetails($order);
        $setting = $this->fallbackSetting($order->branch_id ?? auth()->user()->branch_id ?? null);

        $deliveryIds = $request->query('delivery_ids');
        if ($deliveryIds) {
            $ids = array_filter(array_map('trim', explode(',', $deliveryIds)));
            $deliveries = Delivery::whereIn('id', $ids)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->get();
        } else {
            $deliveries = Delivery::where('order_id', $order->id)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        return view('sales.delivery-challan', compact('order', 'deliveries', 'setting'));
    }

    // Generate delivery challan PDF
    public function deliveryChallanPdf($id, Request $request)
    {
        $order = Order::with(['user.userDetail', 'order_items.product'])->find($id);
        if (! $order) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $this->attachOrderCustomerDetails($order);
        $setting = $this->fallbackSetting($order->branch_id ?? auth()->user()->branch_id ?? null);

        $deliveryIds = $request->query('delivery_ids');
        if ($deliveryIds) {
            $ids = array_filter(array_map('trim', explode(',', $deliveryIds)));
            $deliveries = Delivery::whereIn('id', $ids)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->get();
        } else {
            $deliveries = Delivery::where('order_id', $order->id)
                ->with(['orderItem.product.unit', 'product.unit', 'deliveredBy'])
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        $data = [
            'order' => $order,
            'deliveries' => $deliveries,
            'setting' => $setting,
            'challan_number' => 'DC-' . $order->id,
        ];

        $pdf = Pdf::loadView('sales.delivery-challan-pdf', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('delivery_challan_' . $order->id . '.pdf');
    }


    public function sales_report(Request $request)
    {
        $user         = Auth()->user();
        $branchId     = $user->id ?? null;
        $UserBranchId = $user->branch_id ?? null;
        $userRole     = $user->role ?? '';
        $subAdminId   = session('selectedSubAdminId');

        // Decide branch based on role
        if ($userRole === 'sub-admin') {
            $branchIdToUse = $branchId;
        } elseif ($userRole === 'admin' && $subAdminId) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff') {
            $branchIdToUse = $UserBranchId;
        } else {
            $branchIdToUse = $branchId;
        }

        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name');
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();

        // ✅ Fetch categories based on branch
        if ($userRole === 'staff') {
            $categories = Category::where('isDeleted', 0)
                ->where('branch_id', $UserBranchId)
                ->orderBy('name')
                ->get();
        } else {
            $categories = Category::where('isDeleted', 0)
                ->where('branch_id', $branchIdToUse)
                ->orderBy('name')
                ->get();
        }

        // ✅ Fetch brands based on branch
        $brands = \App\Models\Brand::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('name')
            ->get();

        $staffList = collect();
        if (in_array($userRole, ['admin', 'sub-admin'], true)) {
            $staffList = User::where('role', 'staff')
                ->where('isDeleted', 0)
                ->where('branch_id', $branchIdToUse)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('sales/salesreport', compact('customers', 'categories', 'brands', 'staffList'));
    }
    public function pos()
    {
        $user       = auth()->user();
        $userRole   = $user->role ?? '';
        $userId     = $user->id ?? null;
        $branchId   = $user->branch_id ?? null;
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide which branch_id to use
        if ($userRole === 'sub-admin' && $userId) {
            $branchIdToUse = $userId;
        } elseif ($userRole === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff' && $branchId) {
            $branchIdToUse = $branchId;
        } else {
            $branchIdToUse = $userId;
        }
        // dd($branchIdToUse);
        // 🔹 Get settings
        $setting           = Setting::where('branch_id', $branchIdToUse)->first();
        $currency_symbol   = $setting->currency_symbol ?? '₹';
        $currency_position = $setting->currency_position ?? 'left';

        // 🔹 Common queries
        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->orderBy('id', 'desc')
            ->get();
        $taxRates = TaxRate::where('status', 'active')->where('isDeleted', 0)
            ->where('branch_id', $branchIdToUse)
            ->get();

        // 🔹 Banks
        $banks = BankMaster::where('branch_id', $branchIdToUse)
            ->where('status', 1)
            ->where('isDeleted', 0)
            ->get();

        // $customers = User::where('role', 'customer')
        //     ->where('branch_id', $branchIdToUse)
        //     ->where('isDeleted', 0)
        //     ->get();
        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0);
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();
        // dd($customers);
        // 🔹 Vendors only for Staff or default case
        $vendors = collect(); // empty collection if not needed
        if ($userRole === 'staff' || $userRole === 'admin' || $userRole === 'sub-admin') {
            $vendors = User::where('role', 'vendor')
                ->where('branch_id', $branchIdToUse)
                ->where('isDeleted', 0)
                ->get();
        }

        // Staff list for assignment dropdown (all users)
        $staffList = User::where('role', 'staff')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $convertOrderId = request('convert_order_id');
        $convertOrder = null;
        $convertOrderAdvancePaid = 0;
        if ($convertOrderId) {
            $convertOrder = \App\Models\Order::with(['order_items' => function ($query) {
                $query->select(
                    'id',
                    'order_id',
                    'product_id',
                    'product_gst_details',
                    'product_gst_total',
                    'quantity',
                    'price',
                    'discount_percentage',
                    'discount_amount',
                    'total_amount'
                );
            }, 'order_items.product'])->find($convertOrderId);

            if ($convertOrder) {
                $convertOrderAdvancePaid = \App\Models\PaymentStore::where('order_id', $convertOrderId)->sum('payment_amount');
            }
        }

        return view('sales.pos', compact(
            'categories',
            'taxRates',
            'customers',
            'vendors',
            'currency_symbol',
            'currency_position',
            'setting',
            'banks',
            'staffList',
            'userRole',
            'convertOrder',
            'convertOrderAdvancePaid'
        ));
    }

    public function sale_report($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // Eager load related models
        $sales = OrderItem::with('product.category', 'invoice', 'user.userDetail')
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No sales data found.');
        }

        // Get settings
        $settings         = Setting::where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // Process each sale
        $totalAmount    = 0;
        $discountAmount = 0;
        $taxDetails     = [];
        // 🔹 GST / Tax Calculation
        // $taxDetails     = [];
        $totalTaxAmount = 0;

        foreach ($sales as $sale) {
            $gstDetails = $sale->product_gst_details;
            $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
            $rowTaxes     = [];
            $rowTaxAmount = 0;

            if (is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                    $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                    $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                    if ($rate > 0 || $amount > 0) {
                        $rowTaxes[] = [
                            'name'   => strtoupper($name),
                            'rate'   => $rate,
                            'amount' => $amount,
                        ];
                        $rowTaxAmount += $amount;

                        // accumulate overall tax totals for summary
                        $taxKey = strtolower($name);
                        if (! isset($taxDetails[$taxKey])) {
                            $taxDetails[$taxKey] = [
                                'name'   => strtoupper($name),
                                'rate'   => $rate,
                                'amount' => 0,
                            ];
                        }
                        $taxDetails[$taxKey]['amount'] += $amount;
                    }
                }
            }

            // Attach to sale row
            $sale->rowGSTOption = $rowGSTOption;
            $sale->rowTaxes     = $rowTaxes;
            $sale->rowTaxAmount = $rowTaxAmount;

            // Final total per row = database total_amount (which includes GST and discount)
            $sale->rowFinalTotal = $sale->total_amount;

            $totalAmount = $sales->sum('rowFinalTotal');
        }
        $totalAmount = round($totalAmount);

        // Customer info (from first sale)
        $customer    = $sales->first()->user ?? null;
        $userDetails = $customer ? $customer->userDetail : null;

        return view('sales.sale_report', compact(
            'sales',
            'settings',
            'discountAmount',
            'totalAmount',
            'taxDetails',
            'currencySymbol',
            'currencyPosition',
            'customer',
            'userDetails',
            'ids'
        ));
    }

    public function export_sales_report_pdf($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // 🔹 Eager load related models
        $sales = OrderItem::with('product.category', 'invoice', 'user.userDetail')
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No sales data found.');
        }

        // 🔹 Get settings
        $setting          = Setting::where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        $subtotal = $sales->sum('total_amount');

        // 🔹 Discount calculation
        $discountAmount = 0;
        foreach ($sales as $sale) {
            if ($sale->invoice && $sale->invoice->discount) {
                $discountPercent = $sale->invoice->discount;
                $discountAmount += ($sale->total_amount * $discountPercent) / 100;
            }
        }

        $subtotalAfterDiscount = $subtotal - $discountAmount;

        // 🔹 GST / Tax Calculation
        $taxDetails     = [];
        $totalTaxAmount = 0;

        foreach ($sales as $sale) {
            $gstDetails = $sale->product_gst_details;
            $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
            $rowTaxes     = [];
            $rowTaxAmount = 0;

            if (is_array($gstDetails)) {
                foreach ($gstDetails as $tax) {
                    $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                    $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                    $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                    if ($rate > 0 || $amount > 0) {
                        $rowTaxes[] = [
                            'name'   => strtoupper($name),
                            'rate'   => $rate,
                            'amount' => $amount,
                        ];
                        $rowTaxAmount += $amount;

                        // accumulate overall tax totals for summary
                        $taxKey = strtolower($name);
                        if (! isset($taxDetails[$taxKey])) {
                            $taxDetails[$taxKey] = [
                                'name'   => strtoupper($name),
                                'rate'   => $rate,
                                'amount' => 0,
                            ];
                        }
                        $taxDetails[$taxKey]['amount'] += $amount;
                    }
                }
            }

            // Attach to sale row
            $sale->rowGSTOption = $rowGSTOption;
            $sale->rowTaxes     = $rowTaxes;
            $sale->rowTaxAmount = $rowTaxAmount;

            // Final total per row = database total_amount (which includes GST and discount)
            $sale->rowFinalTotal = $sale->total_amount;

            $totalTaxAmount += $rowTaxAmount;
        }

        // 🔹 Total after discount + taxes
        $totalAmount = $sales->sum('rowFinalTotal');
        $totalAmount = round($totalAmount);

        $pdfData = [
            'sales'            => $sales,
            'setting'          => $setting,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'subtotal'         => $subtotal,
            'discountAmount'   => $discountAmount,
            // 'afterDiscount'    => $afterDiscount,
            'taxDetails'       => $taxDetails,
            'totalTaxAmount'   => $totalTaxAmount,
            'totalAmount'      => $totalAmount,
            // 'ids' => $ids
        ];

        // Load PDF
        $pdf = PDF::loadView('sales.sales-invoice-report-pdf', $pdfData)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('sales_report.pdf');
    }

    public function export_sales_report_excel($ids)
    {
        $authUser   = auth()->user();
        $subAdminId = session('selectedSubAdminId');

        // 🔹 Decide branch_id based on role
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            $branchIdToUse = $authUser->branch_id;
        } elseif ($authUser->role === 'admin' && ! empty($subAdminId)) {
            $branchIdToUse = $subAdminId;
        } else {
            $branchIdToUse = $authUser->id;
        }

        $idsArray = explode(',', $ids);

        // 🔹 Eager load related models
        $sales = OrderItem::with('product.category', 'invoice', 'user.userDetail')
            ->whereIn('id', $idsArray)
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.report')->with('error', 'No sales data found.');
        }

        $filename = 'sales_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Order Number',
            'Product',
            'Customer Name',
            'Category',
            'Original Price',
            'Discount (%)',
            'Final Unit Price',
            'Quantity',
            'Taxes',
            'Total'
        ];

        $callback = function() use ($sales, $columns, $branchIdToUse) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($sales as $sale) {
                $discountPercent = $sale->invoice->discount ?? 0;
                $originalUnitPrice = $sale->price;
                $discountPerUnit = ($originalUnitPrice * $discountPercent) / 100;
                $finalUnitPrice = $originalUnitPrice - $discountPerUnit;
                $finalTotal = $finalUnitPrice * $sale->quantity;

                $gstDetails = $sale->product_gst_details;
                $rowGSTOption = !empty($gstDetails) ? 'with_gst' : 'without_gst';
                $rowTaxAmount = 0;
                $taxDetailsStr = 'N/A';

                if (is_array($gstDetails)) {
                    $taxes = [];
                    foreach ($gstDetails as $tax) {
                        $name = $tax['tax_name'] ?? ($tax['name'] ?? 'GST');
                        $rate = $tax['tax_rate'] ?? ($tax['rate'] ?? 0);
                        $amount = $tax['tax_amount'] ?? ($tax['amount'] ?? 0);

                        if ($rate > 0 || $amount > 0) {
                            $rowTaxAmount += $amount;
                            $taxes[] = strtoupper($name) . " (" . $rate . "%): " . number_format($amount, 2);
                        }
                    }
                    if (!empty($taxes)) {
                        $taxDetailsStr = implode(', ', $taxes);
                    }
                }

                $rowFinalTotal = $sale->total_amount;

                $row = [
                    $sale->invoice->order_number ?? 'N/A',
                    $sale->product->name ?? '-',
                    $sale->user->name ?? 'N/A',
                    $sale->product->category->name ?? 'N/A',
                    number_format((float)$originalUnitPrice, 2, '.', ''),
                    $discountPercent . '%',
                    number_format((float)$finalUnitPrice, 2, '.', ''),
                    $sale->quantity,
                    $taxDetailsStr,
                    number_format((float)$rowFinalTotal, 2, '.', ''),
                ];

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show_sales_report_page(Request $request)
    {
        try {
            // 🔹 Get inputs directly from the request
            $ids      = $request->input('ids');
            $branchId = $request->input('branch');

            // 🔹 Validate required parameters
            if (empty($ids)) {
                abort(404, 'No sales selected.');
            }
            if (empty($branchId)) {
                abort(404, 'Branch ID is missing.');
            }

            // 🔹 Convert to array if comma-separated
            $idsArray = explode(',', $ids);

            // 🔹 Fetch sales data with relationships
            $sales = OrderItem::with('product.category', 'invoice', 'user.userDetail')
                ->whereIn('id', $idsArray)
                ->get();

            if ($sales->isEmpty()) {
                abort(404, 'No sales found.');
            }

            // 🔹 Fetch settings for branch
            $settings         = Setting::where('branch_id', $branchId)->first();
            $currencySymbol   = $settings->currency_symbol ?? '₹';
            $currencyPosition = $settings->currency_position ?? 'left';

            // 🔹 Initialize totals
            $taxDetails     = [];
            $totalAmount    = 0;
            $discountAmount = 0;

            foreach ($sales as $sale) {
                $rowTaxes     = [];
                $rowTaxAmount = 0;

                $rowGSTOption   = $sale->invoice->gst_option ?? 'without_gst';
                $rowTaxIds      = $sale->invoice->tax_id;
                $rowTaxIdsArray = is_array($rowTaxIds) ? $rowTaxIds : (json_decode($rowTaxIds, true) ?: []);

                // 🔹 Fetch applicable tax rates
                $rowTaxRates = collect();
                if ($rowGSTOption === 'with_gst' && ! empty($rowTaxIdsArray)) {
                    $rowTaxRates = TaxRate::where('status', 'active')
                        ->where('branch_id', $branchId)
                        ->where('isDeleted', 0)
                        ->whereIn('id', $rowTaxIdsArray)
                        ->get();
                }

                // 🔹 Apply discount (if any)
                $unitPrice = $sale->price;
                if ($sale->invoice && $sale->invoice->discount) {
                    $discountPercent = $sale->invoice->discount;
                    $discountPerUnit = ($unitPrice * $discountPercent) / 100;
                    $unitPrice -= $discountPerUnit;
                    $discountAmount += $discountPerUnit * $sale->quantity;
                }

                // 🔹 Calculate taxes per item
                foreach ($rowTaxRates as $tax) {
                    $taxBase = $unitPrice * $sale->quantity;
                    $amount  = $taxBase * ($tax->tax_rate / 100);

                    $rowTaxes[] = [
                        'name'   => $tax->tax_name,
                        'rate'   => $tax->tax_rate,
                        'amount' => $amount,
                    ];

                    $rowTaxAmount += $amount;

                    // Accumulate total tax details
                    if (! isset($taxDetails[$tax->id])) {
                        $taxDetails[$tax->id] = [
                            'name'   => $tax->tax_name,
                            'rate'   => $tax->tax_rate,
                            'amount' => 0,
                        ];
                    }
                    $taxDetails[$tax->id]['amount'] += $amount;
                }

                // 🔹 Attach row-level summary
                $sale->rowGSTOption = $rowGSTOption;
                $sale->rowTaxes     = $rowTaxes;
                $sale->rowTaxAmount = $rowTaxAmount;

                // 🔹 Final total per item
                $sale->rowFinalTotal = ($unitPrice * $sale->quantity) + $rowTaxAmount;
            }

            // 🔹 Calculate final grand total
            $totalAmount = $sales->sum('rowFinalTotal');
            $totalAmount = round($totalAmount);

            // 🔹 Customer info (first sale user)
            $customer    = $sales->first()->user ?? null;
            $userDetails = $customer ? $customer->userDetail : null;

            // 🔹 Prepare data for Blade view
            $data = [
                'sales'            => $sales,
                'settings'         => $settings,
                'discountAmount'   => $discountAmount,
                'totalAmount'      => $totalAmount,
                'taxDetails'       => $taxDetails,
                'currencySymbol'   => $currencySymbol,
                'currencyPosition' => $currencyPosition,
                'customer'         => $customer,
                'userDetails'      => $userDetails,
                'ids'              => $ids,
            ];

            // ✅ Return view without requiring authentication
            return view('sales.web_sale_report', $data);
        } catch (\Throwable $e) {
            abort(500, 'Error loading sales report: ' . $e->getMessage());
        }
    }

    public function tds_report(Request $request)
    {
        $user         = Auth()->user();
        $branchId     = $user->id ?? null;
        $userBranchId = $user->branch_id ?? null;
        $userRole     = $user->role ?? '';
        $subAdminId   = session('selectedSubAdminId');

        if ($userRole === 'sub-admin') {
            $branchIdToUse = $branchId;
        } elseif ($userRole === 'admin' && $subAdminId) {
            $branchIdToUse = $subAdminId;
        } elseif ($userRole === 'staff') {
            $branchIdToUse = $userBranchId;
        } else {
            $branchIdToUse = $branchId;
        }

        $customersQuery = User::where('role', 'customer')
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->orderBy('name');
        StaffDepartmentScope::applyStaffCreatedByScope($customersQuery, $user);
        $customers = $customersQuery->get();

        return view('sales.tdsreport', compact('customers'));
    }
}
