<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Purchases;
use App\Models\TaxRate;
use App\Models\Unit;
use App\Models\User;
use App\Services\StaffDepartmentScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private function isMobileCategoryId($categoryId): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        $categoryName = strtolower(trim((string) Category::where('id', $categoryId)->value('name')));
        return str_contains($categoryName, 'mobile');
    }

    private function isWarrantyCategoryId($categoryId): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        return strtolower(trim((string) Category::where('id', $categoryId)->value('name'))) === 'warranty';
    }

    public function getAllProduct(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $request->sub_branch_id ?: $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));
        $shouldPaginate = $request->has('page') || $request->has('per_page') || $request->filled('search');

        $query = Product::query()
            ->select([
                'id',
                'name',
                'SKU',
                'price',
                'quantity',
                'unit_id',
                'category_id',
                'brand_id',
                'branch_id',
                'images',
            ])
            ->with([
                'category:id,name',
                'brand:id,name',
                'unit:id,unit_name',
            ])
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
            ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('SKU', 'LIKE', "%{$search}%")
                        ->orWhere('barcode', 'LIKE', "%{$search}%");
                });
            });

        $pagination = null;

        if ($shouldPaginate) {
            $paginatedProducts = $query
                ->latest('id')
                ->paginate($perPage, ['*'], 'page', $page);

            $products = $paginatedProducts->items();

            $pagination = [
                'current_page' => $paginatedProducts->currentPage(),
                'last_page' => $paginatedProducts->lastPage(),
                'per_page' => $paginatedProducts->perPage(),
                'total' => $paginatedProducts->total(),
                'next_page_url' => $paginatedProducts->nextPageUrl(),
                'prev_page_url' => $paginatedProducts->previousPageUrl(),
            ];
        } else {
            $products = $query
                ->latest('id')
                ->get();
        }

        $settingsQuery = DB::table('settings')
            ->select('low_stock', 'currency_symbol', 'currency_position');

        if ($branchId) {
            $settingsQuery->where('branch_id', $branchId);
        }

        $settings = $settingsQuery->first();

        if (! $settings) {
            $settings = DB::table('settings')
                ->select('low_stock', 'currency_symbol', 'currency_position')
                ->first();
        }

        return response()->json([
            'status'            => true,
            'data'              => $products,
            'pagination'        => $pagination,
            'currencySymbol'    => $settings->currency_symbol ?? 'â‚¹',
            'currencyPosition'  => $settings->currency_position ?? 'left',
            'lowStockThreshold' => $settings->low_stock ?? 0,
        ]);
    }

    public function export_product(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1ï¸âƒ£ Resolve Branch ID (single, clean logic)
     -------------------------------------------------*/
        $branchId = match ($user->role) {
            'staff' => $user->branch_id,
            'admin' => $request->selectedSubAdminId ?: $user->id,
            default => $user->id,
        };

        /* -------------------------------------------------
     | 2ï¸âƒ£ Fetch Products
     -------------------------------------------------*/
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units','products.unit_id', '=', 'units.id')
            ->select([
                'products.name as product_name',
                'products.SKU',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.description',
                'products.created_at',
                'categories.name as category_name',
                'brands.name as brand_name',
                'units.unit_name'
            ])
            ->where('products.isDeleted', 0)
            ->where('products.branch_id', $branchId)
            ->when(
                $request->category_id,
                fn($q) =>
                $q->where('products.category_id', $request->category_id)
            )
            ->when(
                $request->brand_id,
                fn($q) =>
                $q->where('products.brand_id', $request->brand_id)
            )
            ->when(
                $request->unit_id,
                fn($q) =>
                $q->where('products.unit_id', $request->unit_id)
            )
            ->latest('products.id')
            ->get();

        /* -------------------------------------------------
     | 3ï¸âƒ£ Currency Settings
     -------------------------------------------------*/
        $settings = DB::table('settings')
            ->where('branch_id', $branchId)
            ->first();

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? 'â‚¹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
        $currencyPosition = $settings->currency_position ?? 'left';

            // Helper function for Indian number formatting
    $formatIndianCurrency = function($amount) {
        if ($amount === null || $amount === '-') return '-';

        $amount = (float)$amount;
        $amount = number_format($amount, 2, '.', '');

        // Split into whole and decimal parts
        $parts = explode('.', $amount);
        $whole = $parts[0];
        $decimal = $parts[1];

        // Indian numbering system formatting
        $lastThree = substr($whole, -3);
        $otherNumbers = substr($whole, 0, -3);

        if ($otherNumbers != '') {
            $otherNumbers = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $otherNumbers);
            $formattedWhole = $otherNumbers . ',' . $lastThree;
        } else {
            $formattedWhole = $lastThree;
        }

        return $formattedWhole . '.' . $decimal;
    };


        /* -------------------------------------------------
     | 4ï¸âƒ£ Create Excel
     -------------------------------------------------*/
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Product Name',
            'B1' => 'SKU',
            // 'C1' => 'Barcode',
            'C1' => 'Category',
            'D1' => 'Brand',
            'E1' => 'Quantity',
            'F1' => 'Unit',
            'G1' => 'Price',
            'H1' => 'Description',
            'I1' => 'Created At',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

          // Set column widths for better readability
    $sheet->getColumnDimension('A')->setWidth(30); // Product Name
    $sheet->getColumnDimension('B')->setWidth(15); // SKU
    // $sheet->getColumnDimension('C')->setWidth(15); // Barcode
    $sheet->getColumnDimension('C')->setWidth(15); // Category
    $sheet->getColumnDimension('D')->setWidth(15); // Brand
    $sheet->getColumnDimension('E')->setWidth(12); // Quantity
    $sheet->getColumnDimension('F')->setWidth(12); // Unit
    $sheet->getColumnDimension('G')->setWidth(18); // Price
    $sheet->getColumnDimension('H')->setWidth(30); // Description
    $sheet->getColumnDimension('I')->setWidth(15); // Created At

        // Data
        $row = 2;
        foreach ($products as $product) {
             // Format price with Indian number system
        $formattedPrice = $formatIndianCurrency($product->price);

              $price = $currencyPosition === 'left'
            ? "{$currencySymbol}{$formattedPrice}"
            : "{$formattedPrice}{$currencySymbol}";

            $sheet->fromArray([
                $product->product_name,
                $product->SKU,
                // $product->barcode,
                $product->category_name ?? '-',
                $product->brand_name ?? '-',
                $product->quantity ?? '-',
                $product->unit_name ?? '-',
                $price,
                $product->description ?? '-',
                \Carbon\Carbon::parse($product->created_at)->format('d-m-Y'),
            ], null, "A{$row}");
             // Set the price column as text to preserve formatting
        $sheet->getStyle('G' . $row)->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

            $row++;
        }

        /* -------------------------------------------------
     | 5ï¸âƒ£ Save File
     -------------------------------------------------*/
        $fileName     = 'Products_' . now()->format('Ymd_His') . '.xlsx';
        $folder       = 'product-exports';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path("app/public/{$relativePath}"));

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        /* -------------------------------------------------
     | 6ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'    => true,
            'message'   => 'Product Excel exported successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function export_product_pdf(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1ï¸âƒ£ Resolve Branch ID (single source of truth)
     -------------------------------------------------*/
        $branchId = match ($user->role) {
            'staff' => $user->branch_id,
            'admin' => $request->selectedSubAdminId ?: $user->id,
            default => $user->id,
        };

        /* -------------------------------------------------
     | 2ï¸âƒ£ Build Product Query
     -------------------------------------------------*/
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units','products.unit_id', '=', 'units.id')
            ->select([
                'products.id',
                'products.name as product_name',
                'products.SKU',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.description',
                'products.created_at',
                'categories.name as category_name',
                'brands.name as brand_name',
                'units.unit_name'
            ])
            ->where('products.isDeleted', 0)
            ->where('products.branch_id', $branchId)
            ->when(
                $request->category_id,
                fn($q) =>
                $q->where('products.category_id', $request->category_id)
            )
            ->when(
                $request->brand_id,
                fn($q) =>
                $q->where('products.brand_id', $request->brand_id)
            )
            ->latest('products.id')
            ->get();
    // dd($products);
        /* -------------------------------------------------
     | 3ï¸âƒ£ Settings (currency, GST, branding)
     -------------------------------------------------*/
        $settings = DB::table('settings')
            ->where('branch_id', $branchId)
            ->first();

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? 'â‚¹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );

        /* -------------------------------------------------
     | 4ï¸âƒ£ PDF Generation
     -------------------------------------------------*/
        $pdf = Pdf::loadView('product.product_pdf', [
            'products'         => $products,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $settings->currency_position ?? 'left',
            'settings'         => $settings,
            'userName'         => $user->name ?? 'N/A',
            'gstNum'           => $user->gst_number ?? 'N/A',
        ])->setPaper('A4', 'portrait');

        /* -------------------------------------------------
     | 5ï¸âƒ£ Store PDF
     -------------------------------------------------*/
        $fileName     = 'Products_' . now()->format('Ymd_His') . '.pdf';
        $folder       = 'all-products';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($relativePath, $pdf->output());

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        /* -------------------------------------------------
     | 6ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'    => true,
            'message'   => 'Product PDF generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function getProductById($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);
        if ($product) {
            return response()->json(['status' => true, 'product' => $product], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }
    }

    public function createProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1ï¸âƒ£ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $request->branch_id ?? $user->id,
            default     => $user->id,
        };

        // Optional override
        if (! empty($request->sub_admin_id) && strtolower($user->role) !== 'staff') {
            $branchId = $request->sub_admin_id;
        }

        $isWarrantyCategory = $this->isWarrantyCategoryId($request->category_id);

        if ($isWarrantyCategory) {
            $request->merge([
                'price' => $request->filled('price') ? $request->price : 0,
                'quantity' => $request->filled('quantity') ? $request->quantity : 0,
            ]);
        }

        $rules = [
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required|numeric|gt:0',
            'SKU' => [
                'nullable',
                'string',

                Rule::unique('products', 'SKU')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // ðŸ‘ˆ IMPORTANT
                    }),
            ],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId)),],
            'hsn_code' => 'nullable',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0);
                    }),
            ],
            'quantity'      => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required|numeric|gte:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp',
            'branch_id'     => 'nullable|numeric',
            'sub_admin_id'  => 'nullable|numeric',
            'imei_no'       => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'          => 'unit',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->gst_option === 'with_gst') {
                $productGst = $request->product_gst;
                if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
                    $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
                }
            }

            if ($this->isMobileCategoryId($request->category_id)) {
                $qty = (float) ($request->quantity ?? 0);
                $imeiNoJson = $request->imei_no;
                if ($qty > 0) {
                    if (empty($imeiNoJson) || $imeiNoJson === '[]') {
                        $validator->errors()->add('imei_no', 'Please enter IMEI/Serial numbers for the selected mobile quantity.');
                    } else {
                        $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                        if (!is_array($imeiArray)) {
                            $validator->errors()->add('imei_no', 'Invalid IMEI numbers format.');
                        } else {
                            $nonEmptyImeis = array_filter(array_map('trim', $imeiArray));
                            if (count($nonEmptyImeis) != $qty) {
                                $validator->errors()->add('imei_no', 'The number of IMEI numbers must match the quantity (' . $qty . ').');
                            }

                            // Duplicate checking within form
                            $allImeis = [];
                            foreach ($nonEmptyImeis as $imei) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' is duplicated in this form.');
                                }
                                $allImeis[] = $imei;

                                // Duplicate checking in database (products)
                                $existsInProducts = DB::table('products')
                                    ->where('isDeleted', 0)
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($existsInProducts) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' already exists in products.');
                                }
                            }
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2ï¸âƒ£ Upload Images
     -------------------------------------------------*/
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('img/product', 'public');
            }
        }

        /* -------------------------------------------------
     | 3ï¸âƒ£ Resolve Branch ID (clean & safe)
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $validated['branch_id'] ?? $user->id,
            default     => $user->id,
        };

        // Optional override (admin / sub-admin only)
        if (! empty($validated['sub_admin_id']) && strtolower($user->role) !== 'staff') {
            $branchId = $validated['sub_admin_id'];
        }

        /* -------------------------------------------------
     | 4ï¸âƒ£ Availability Logic
     -------------------------------------------------*/
        $availability = $validated['quantity'] == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 5ï¸âƒ£ Create Product
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        $product = Product::create([
            'vendor_id'              => $validated['vendor_id'] ?? 1,
            'category_id'            => $validated['category_id'],
            'brand_id'               => $validated['brand_id'],
            'branch_id'              => $branchId,
            'name'                   => $validated['name'],
            'description'            => $validated['description'] ?? null,
            'price'                  => $validated['price'],
            'cost_price'             => $validated['cost_price'] ?? null,
            'landing_cost'           => $validated['landing_cost'] ?? null,
            'product_code'           => $validated['product_code'] ?? null,
            'supplier_code'          => $validated['supplier_code'] ?? null,
            'international_code'     => $validated['international_code'] ?? null,
            'serial_no_status'       => $validated['serial_no_status'] ?? null,
            'non_inventory_type'     => $validated['non_inventory_type'] ?? null,
            'stock_validation_status' => $validated['stock_validation_status'] ?? null,
            'item_type'              => $validated['item_type'] ?? null,
            'discount_print_status'  => $validated['discount_print_status'] ?? null,
            'item_created_on'        => $validated['item_created_on'] ?? null,
            'SKU'                    => $validated['SKU'],
            'hsn_code'               => $validated['hsn_code'],
            'barcode'                => $validated['barcode'] ?? null,
            'quantity'               => $validated['quantity'],
            'imei_no'                => $this->isMobileCategoryId($validated['category_id']) ? ($request->imei_no ?? '[]') : null,
            'unit_id'                => $validated['unit_id'],
            'status'                 => $validated['status'] ?? 'active',
            'availablility'          => $availability,
            'gst_option'             => $validated['gst_option'] ?? 'without_gst',
            'product_gst'            => !empty($gstData) ? json_encode($gstData) : null,
            'images'                 => json_encode($imagePaths),
        ]);

        /* -------------------------------------------------
     | 6ï¸âƒ£ Inventory Log
     -------------------------------------------------*/
        if (! $isWarrantyCategory) {
            ProductInventory::create([
                'product_id'    => $product->id,
                'initial_stock' => $validated['quantity'],
                'current_stock' => $validated['quantity'],
                'branch_id'     => $branchId,
                'create_by'     => $user->id,
                'type'          => 'Create',
                'date'          => now(),
            ]);
        }

        /* -------------------------------------------------
     | 7ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'product' => $product->fresh(),
        ], 200);
    }

    public function updateProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1ï¸âƒ£ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();

        $product = Product::findOrFail($request->product_id);

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'admin'     => $request->branch_id ?? $product->branch_id,
            'staff'     => $product->branch_id,
            default     => $product->branch_id,
        };

        $isWarrantyCategory = $this->isWarrantyCategoryId($request->category_id);

        if ($isWarrantyCategory) {
            $request->merge([
                'price' => $request->filled('price') ? $request->price : 0,
                'quantity' => $request->filled('quantity') ? $request->quantity : 0,
            ]);
        }

        $rules = [
            'product_id'    => 'required|exists:products,id',
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required|numeric|gt:0',
            'SKU' => [
                'nullable',
                'max:255',
                Rule::unique('products', 'SKU')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // ðŸ‘ˆ ADD THIS
                    })
                    ->ignore($request->product_id),
            ],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId))->ignore($request->product_id),],
            'hsn_code' => 'nullable',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // ðŸ‘ˆ ADD THIS
                    })
                    ->ignore($request->product_id),
            ],
            'quantity'      => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required|numeric|gte:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp',
            'capacity'      => 'nullable|string',
            'voltage'       => 'nullable|string',
            'warranty'      => 'nullable|string',
            'expiry_date'   => 'nullable|date',
            'imei_no'       => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'       => 'unit',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->gst_option === 'with_gst') {
                $productGst = $request->product_gst;
                if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
                    $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
                }
            }

            if ($this->isMobileCategoryId($request->category_id)) {
                $qty = (float) ($request->quantity ?? 0);
                $imeiNoJson = $request->imei_no;
                if ($qty > 0) {
                    if (empty($imeiNoJson) || $imeiNoJson === '[]') {
                        $validator->errors()->add('imei_no', 'Please enter IMEI/Serial numbers for the selected mobile quantity.');
                    } else {
                        $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                        if (!is_array($imeiArray)) {
                            $validator->errors()->add('imei_no', 'Invalid IMEI numbers format.');
                        } else {
                            $nonEmptyImeis = array_filter(array_map('trim', $imeiArray));
                            if (count($nonEmptyImeis) != $qty) {
                                $validator->errors()->add('imei_no', 'The number of IMEI numbers must match the quantity (' . $qty . ').');
                            }

                            // Duplicate checking within form
                            $allImeis = [];
                            foreach ($nonEmptyImeis as $imei) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' is duplicated in this form.');
                                }
                                $allImeis[] = $imei;

                                // Duplicate checking in database (products)
                                $existsInProducts = DB::table('products')
                                    ->where('id', '!=', $request->product_id ?? 0)
                                    ->where('isDeleted', 0)
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($existsInProducts) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' already exists in products.');
                                }
                            }
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2ï¸âƒ£ Fetch Product
     -------------------------------------------------*/
        // $product = Product::findOrFail($validated['product_id']);

        /* -------------------------------------------------
     | 3ï¸âƒ£ Handle Images
     -------------------------------------------------*/
        $existingImages = json_decode($product->images, true) ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $existingImages[] = $image->store('img/product', 'public');
            }
        }

        /* -------------------------------------------------
     | 4ï¸âƒ£ Resolve Branch ID
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();

        $branchId = match ($user->role) {
            'sub-admin' => $user->id,
            'admin'     => $request->branch_id ?? $product->branch_id,
            default     => $product->branch_id,
        };

        /* -------------------------------------------------
     | 5ï¸âƒ£ Availability Auto-Logic
     -------------------------------------------------*/
        $availability = $validated['quantity'] == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 6ï¸âƒ£ Prepare GST Data
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        /* -------------------------------------------------
     | 7ï¸âƒ£ Update Product
     -------------------------------------------------*/
        $oldQuantity = (float) $product->quantity;

        $product->update([
            'vendor_id'              => $validated['vendor_id'] ?? 1,
            'category_id'            => $validated['category_id'],
            'brand_id'               => $validated['brand_id'],
            'branch_id'              => $branchId,
            'name'                   => $validated['name'],
            'description'            => $validated['description'] ?? null,
            'price'                  => $validated['price'],
            'cost_price'             => $validated['cost_price'] ?? null,
            'landing_cost'           => $validated['landing_cost'] ?? null,
            'product_code'           => $validated['product_code'] ?? null,
            'supplier_code'          => $validated['supplier_code'] ?? null,
            'international_code'     => $validated['international_code'] ?? null,
            'serial_no_status'       => $validated['serial_no_status'] ?? null,
            'non_inventory_type'     => $validated['non_inventory_type'] ?? null,
            'stock_validation_status' => $validated['stock_validation_status'] ?? null,
            'item_type'              => $validated['item_type'] ?? null,
            'discount_print_status'  => $validated['discount_print_status'] ?? null,
            'item_created_on'        => $validated['item_created_on'] ?? null,
            'SKU'                    => $validated['SKU'],
            'hsn_code'               => $validated['hsn_code'],
            'barcode'                => $validated['barcode'] ?? null,
            'quantity'               => $validated['quantity'],
            'imei_no'                => $this->isMobileCategoryId($validated['category_id']) ? ($request->imei_no ?? '[]') : null,
            'unit_id'                => $validated['unit_id'],
            'status'                 => $validated['status'] ?? $product->status,
            'availablility'          => $availability,
            'gst_option'             => $validated['gst_option'] ?? 'without_gst',
            'product_gst'            => !empty($gstData) ? json_encode($gstData) : null,
            'images'                 => json_encode($existingImages),
            'capacity'               => $validated['capacity'] ?? null,
            'voltage'                => $validated['voltage'] ?? null,
            'warranty'               => $validated['warranty'] ?? null,
            'expiry_date'            => $validated['expiry_date'] ?? null,
        ]);

        /* -------------------------------------------------
     | 8ï¸âƒ£ Inventory Log
     -------------------------------------------------*/
        if (! $isWarrantyCategory) {
            ProductInventory::create([
                'product_id'    => $product->id,
                'initial_stock' => $oldQuantity,
                'current_stock' => $validated['quantity'],
                'branch_id'     => $branchId,
                'create_by'     => $user->id,
                'type'          => 'Edit',
                'date'          => now(),
            ]);
        }

        /* -------------------------------------------------
     | 9ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'product' => $product->fresh(),
        ], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }

        $orderItemExists = OrderItem::where('product_id', $id)->where('isDeleted', 0)->exists();
        $purchaseExists  = Purchases::where('item', $id)->where('isDeleted', 0)->exists();

        if ($orderItemExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing orders and cannot be deleted.',
            ], 409);
        }

        if ($purchaseExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing purchases and cannot be deleted.',
            ], 409);
        }

        // Soft delete: mark isDeleted as 1
        $product->isDeleted = 1;
        $product->save();

        return response()->json(['status' => true, 'message' => 'Product deleted successfully'], 200);
    }

    public function removeProductImage(Request $request)
    {
        $product = Product::find($request->product_id);

        if (! $product) {
            return response()->json(["success" => false, "message" => "Product not found"]);
        }

        $images = json_decode($product->images, true);

        // Check if image exists
        if (($key = array_search($request->image, $images)) !== false) {
            // Remove image from storage
            Storage::delete("storage/img/product" . $request->image);

            // Remove image from array and update database
            unset($images[$key]);
            $product->images = json_encode(array_values($images)); // Re-index array
            $product->save();

            return response()->json(["success" => true, "message" => "Image deleted"]);
        }

        return response()->json(["success" => false, "message" => "Image not found"]);
    }

    public function importProducts(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $user->id;

        $request->validate([
            'csv_file' => 'required|max:10240',
        ]);

        $file      = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());

        $insertedCount = 0;
        $updatedSKUs   = [];
        $invalidSKUs   = [];
        $skippedRows   = [];

        // ── XLSX / XLS path ───────────────────────────────────────────────────
        if (in_array($extension, ['xlsx', 'xls'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $maxRow      = $sheet->getHighestRow();

            // Find the actual column-header row (contains "Item Code" or "S.No" or "Item/Model")
            $headerRowNum = null;
            $headerMap    = [];
            for ($r = 1; $r <= min($maxRow, 20); $r++) {
                $cells = [];
                for ($c = 1; $c <= 30; $c++) {
                    $val = (string) ($sheet->getCellByColumnAndRow($c, $r)->getValue() ?? '');
                    $cells[$c] = strtolower(trim($val));
                }
                
                $foundHeader = false;
                foreach ($cells as $cellVal) {
                    if (str_contains($cellVal, 'item/model code') || 
                        str_contains($cellVal, 'item code') || 
                        str_contains($cellVal, 'item/model desc') || 
                        str_contains($cellVal, 'product*') ||
                        str_contains($cellVal, 'selling price') ||
                        str_contains($cellVal, 's.no')) {
                        $foundHeader = true;
                        break;
                    }
                }

                if ($foundHeader) {
                    $headerRowNum = $r;
                    foreach ($cells as $colIdx => $colName) {
                        if ($colName !== '') {
                            $headerMap[$colName] = $colIdx;
                        }
                    }
                    break;
                }
            }

            if (!$headerRowNum) {
                return response()->json(['status' => false, 'message' => 'Could not find the header row in the Excel file.']);
            }

            $xlsCol = function (array $rowCells, array $aliases, $default = null) use ($headerMap) {
                foreach ($aliases as $alias) {
                    $key = strtolower(trim($alias));
                    if (isset($headerMap[$key])) {
                        $val = trim((string) ($rowCells[$headerMap[$key]] ?? ''));
                        if ($val !== '' && $val !== '-') return $val;
                    }
                }
                return $default;
            };

            DB::beginTransaction();
            try {
                for ($r = $headerRowNum + 1; $r <= $maxRow; $r++) {
                    $rowCells = [];
                    for ($c = 1; $c <= 30; $c++) {
                        $rowCells[$c] = (string) ($sheet->getCellByColumnAndRow($c, $r)->getCalculatedValue() ?? '');
                    }

                    $nonEmpty = array_filter($rowCells, fn($v) => trim($v) !== '' && trim($v) !== '-');
                    if (empty($nonEmpty)) continue;

                    $name              = trim((string) $xlsCol($rowCells, ['item/model desc', 'item/model desc*', 'item/model', 'item/model*', 'item/model description', 'name', 'product name'], ''));
                    $categoryName      = trim((string) $xlsCol($rowCells, ['product', 'product*'], ''));
                    $brandName         = $xlsCol($rowCells, ['brand', 'brand name', 'brand*'], null);
                    $brandName         = $brandName ? trim($brandName) : null;
                    $productCode       = trim((string) $xlsCol($rowCells, ['item/model code', 'item/model code*', 'item code', 'item_code', 'sku', 'sku code', 'product code'], ''));
                    $price             = $xlsCol($rowCells, ['selling price', 'selling price*', 'price', 'product price'], 0);
                    $costPrice         = $xlsCol($rowCells, ['cost price', 'cost price*', 'cost_price'], null);
                    $landingCost       = $xlsCol($rowCells, ['landing cost', 'landing cost*', 'landing_cost'], null);
                    $hsnCode           = $xlsCol($rowCells, ['hsn/sac code', 'hsn/sac code*', 'hsn/sac', 'hsn code', 'hsn', 'hsn_code'], null);
                    $unitName          = $xlsCol($rowCells, ['unit quantity code', 'unit quantity code*', 'uqc', 'unit', 'unit_name'], null);
                    $unitName          = $unitName ? trim($unitName) : null;
                    $status            = strtolower((string) $xlsCol($rowCells, ['status', 'product status'], 'active'));
                    // Additional nullable Excel columns
                    $supplierCode      = $xlsCol($rowCells, ['supplier i/m code', 'supplier i/m code*', 'supplier_code'], null);
                    $intlCode          = $xlsCol($rowCells, ['international i/m code', 'international_code'], null);
                    $serialNoStatus    = $xlsCol($rowCells, ['serial no status', 'serial_no_status'], null);
                    $nonInventoryType  = $xlsCol($rowCells, ['non inventory type', 'non_inventory_type'], null);
                    $stockValStatus    = $xlsCol($rowCells, ['stock validation status', 'stock_validation_status'], null);
                    $itemType          = $xlsCol($rowCells, ['item/type', 'item type', 'item_type'], null);
                    $discountPrint     = $xlsCol($rowCells, ['discount print status', 'discount_print_status'], null);
                    $itemCreatedOn     = $xlsCol($rowCells, ['item created on', 'item_created_on'], null);

                    $qty               = (float) $xlsCol($rowCells, ['qty', 'qty*', 'quantity', 'quantity*', 'qt'], 0);
                    $maintainImei      = strtolower(trim((string) $xlsCol($rowCells, ['maintain imei no:(y/n)', 'maintain imei no', 'maintain imei', 'maintain_imei'], '')));
                    $autoImei          = trim((string) $xlsCol($rowCells, ['auto imei no:(y/n)', 'auto imei no', 'auto imei', 'imei', 'imei no', 'imei_no', 'serial no', 'serial_no'], ''));

                    if (empty($name) || empty($categoryName)) continue;

                    $price       = (float) preg_replace('/[^0-9.]/', '', (string) $price);
                    $costPrice   = $costPrice !== null ? (float) preg_replace('/[^0-9.]/', '', (string) $costPrice) : null;
                    $landingCost = $landingCost !== null ? (float) preg_replace('/[^0-9.]/', '', (string) $landingCost) : null;
                    $status      = in_array($status, ['active', 'inactive'], true) ? $status : 'active';
                    
                    if ($itemCreatedOn) {
                        try {
                            $itemCreatedOn = \Carbon\Carbon::createFromFormat('m/d/Y', $itemCreatedOn)->format('Y-m-d');
                        } catch (\Exception $e) {
                            try {
                                $itemCreatedOn = \Carbon\Carbon::parse($itemCreatedOn)->format('Y-m-d');
                            } catch (\Exception $e2) {
                                $itemCreatedOn = null;
                            }
                        }
                    }

                    // 1️⃣ Category: resolve case-insensitively or create
                    $category = Category::where('branch_id', $branchId)
                        ->where(function($q) use ($categoryName) {
                            $q->where('name', $categoryName)
                              ->orWhere('name', strtolower($categoryName))
                              ->orWhere('name', strtoupper($categoryName));
                        })
                        ->first();
                    if (!$category) {
                        $category = Category::create([
                            'name'      => $categoryName,
                            'branch_id' => $branchId,
                            'isDeleted' => 0
                        ]);
                    }

                    // 2️⃣ Brand: create in brands table first, then get brand_id
                    $brand = null;
                    if ($brandName) {
                        $brand = Brand::firstOrCreate(
                            ['name' => strtolower($brandName), 'branch_id' => $branchId],
                            ['isDeleted' => 0, 'status' => 'active']
                        );
                    }

                    // Auto-create Unit
                    $unit = null;
                    if ($unitName) {
                        $unit = Unit::firstOrCreate(
                            ['unit_name' => strtolower($unitName), 'created_by' => $branchId],
                            ['isDeleted' => 0]
                        );
                    }

                    // Check if product already exists by product_code + branch or by Name + Category
                    $product = null;
                    if (!empty($productCode)) {
                        $product = Product::where('product_code', $productCode)->where('branch_id', $branchId)->where('isDeleted', 0)->first();
                    }
                    if (!$product) {
                        $product = Product::where('name', $name)->where('category_id', $category->id)->where('branch_id', $branchId)->where('isDeleted', 0)->first();
                    }

                    if (empty($productCode)) {
                        if ($product) {
                            $productCode = $product->product_code ?: ($product->SKU ?: 'ITEM' . $product->id);
                        } else {
                            do {
                                $tempCode = 'ITEM' . mt_rand(10000000, 99999999);
                            } while (Product::where('product_code', $tempCode)->where('branch_id', $branchId)->exists());
                            $productCode = $tempCode;
                        }
                    }

                    $isImeiManaged = ($maintainImei === 'y') || $this->isMobileCategoryId($category->id);
                    $imeisList = [];
                    if ($isImeiManaged && !empty($autoImei) && strtolower($autoImei) !== 'n' && $autoImei !== '-') {
                        $splitImeis = preg_split('/[\n\r,;\/]+/', $autoImei);
                        foreach ($splitImeis as $splitImei) {
                            $cleanImei = trim($splitImei);
                            if ($cleanImei !== '' && strtolower($cleanImei) !== 'n') {
                                $imeisList[] = $cleanImei;
                            }
                        }
                    }

                    if ($product) {
                        $oldQty = (float) $product->quantity;
                        $product->quantity += $qty;
                        if ($brand && !$product->brand_id) {
                            $product->brand_id = $brand->id;
                        }
                        if ($isImeiManaged) {
                            $existingImeis = json_decode($product->imei_no ?? '[]', true);
                            if (!is_array($existingImeis)) {
                                $existingImeis = [];
                            }
                            foreach ($imeisList as $imei) {
                                if (!in_array($imei, $existingImeis)) {
                                    $existingImeis[] = $imei;
                                }
                            }
                            $product->imei_no = json_encode($existingImeis);
                        }
                        $product->availablility = $product->quantity > 0 ? 'in_stock' : 'out_stock';
                        $product->save();

                        ProductInventory::create([
                            'product_id'    => $product->id,
                            'initial_stock' => $oldQty,
                            'current_stock' => $product->quantity,
                            'branch_id'     => $branchId,
                            'create_by'     => $user->id,
                            'type'          => 'Edit',
                            'date'          => now(),
                        ]);

                        $updatedSKUs[] = $productCode;
                    } else {
                        $product = Product::create([
                            'name'                   => $name,
                            'category_id'            => $category->id,
                            'brand_id'               => $brand?->id,
                            'product_code'           => $productCode,
                            'SKU'                    => $productCode,
                            'quantity'               => $qty,
                            'unit_id'                => $unit?->id,
                            'price'                  => $price,
                            'cost_price'             => $costPrice,
                            'landing_cost'           => $landingCost,
                            'hsn_code'               => $hsnCode,
                            'status'                 => $status,
                            'availablility'          => $qty > 0 ? 'in_stock' : 'out_stock',
                            'description'            => null,
                            'branch_id'              => $branchId,
                            'supplier_code'          => $supplierCode ?: null,
                            'international_code'     => $intlCode ?: null,
                            'serial_no_status'       => $serialNoStatus ?: null,
                            'non_inventory_type'     => $nonInventoryType ?: null,
                            'stock_validation_status' => $stockValStatus ?: null,
                            'item_type'              => $itemType ?: null,
                            'discount_print_status'  => $discountPrint ?: null,
                            'item_created_on'        => $itemCreatedOn ?: null,
                            'imei_no'                => json_encode($imeisList),
                            'create_by'              => $user->id,
                        ]);

                        ProductInventory::create([
                            'product_id'    => $product->id,
                            'initial_stock' => $qty,
                            'current_stock' => $qty,
                            'branch_id'     => $branchId,
                            'create_by'     => $user->id,
                            'type'          => 'Create',
                            'date'          => now(),
                        ]);

                        $insertedCount++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Excel Import failed: ' . $e->getMessage()]);
            }

        // ── CSV path ──────────────────────────────────────────────────────────
        } else {
            $path  = $file->getRealPath();
            $lines = file($path);

            if (empty($lines)) {
                return response()->json(['status' => false, 'message' => 'CSV file is empty!']);
            }

            $rawHeader = array_map('str_getcsv', [array_shift($lines)])[0];
            $headerMap = [];
            foreach ($rawHeader as $index => $col) {
                $normalised = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col)));
                $headerMap[$normalised] = $index;
            }

            $col = function (array $row, array $aliases, $default = null) use ($headerMap) {
                foreach ($aliases as $alias) {
                    $key = strtolower(trim($alias));
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        $val = trim($row[$headerMap[$key]]);
                        return $val !== '' ? $val : $default;
                    }
                }
                return $default;
            };

            $data = array_map('str_getcsv', $lines);

            DB::beginTransaction();
            try {
                foreach ($data as $row) {
                    if (empty(array_filter($row, fn($v) => trim($v) !== ''))) continue;

                    $name         = trim((string) $col($row, ['product name', 'name'], ''));
                    $categoryName = trim((string) $col($row, ['category name', 'category', 'product'], ''));
                    $brandName    = $col($row, ['brand name', 'brand'], null);
                    $brandName    = $brandName ? trim($brandName) : null;
                    $sku          = (string) $col($row, ['sku', 'sku code', 'item code', 'product code'], '');
                    $quantity     = (float) $col($row, ['quantity', 'product quantity', 'qty'], 0);
                    $unitName     = $col($row, ['unit', 'uqc'], null);
                    $unitName     = $unitName ? trim($unitName) : null;
                    $price        = $col($row, ['price', 'product price', 'selling price'], 0);
                    $status       = strtolower((string) $col($row, ['status', 'product status'], 'active'));
                    $availability = strtolower((string) $col($row, ['availability', 'product availability'], 'in_stock'));
                    $description  = $col($row, ['description'], null);
                    $hsnCode      = $col($row, ['hsn_code', 'hsn code', 'hsn/sac'], null);
                    $maintainImei = strtolower(trim((string) $col($row, ['maintain imei no:(y/n)', 'maintain imei no', 'maintain imei', 'maintain_imei'], '')));
                    $autoImei     = trim((string) $col($row, ['auto imei no:(y/n)', 'auto imei no', 'auto imei', 'imei', 'imei no', 'imei_no', 'serial no', 'serial_no'], ''));

                    if (empty($name) || empty($categoryName)) continue;

                    $price        = (float) preg_replace('/[^0-9.]/', '', (string) $price);
                    $status       = in_array($status, ['active', 'inactive'], true) ? $status : 'active';
                    $availability = in_array($availability, ['in_stock', 'out_stock', 'in stock', 'out stock'], true)
                        ? str_replace(' ', '_', $availability)
                        : 'in_stock';

                    // 1️⃣ Category
                    $category = Category::where('branch_id', $branchId)
                        ->where(function($q) use ($categoryName) {
                            $q->where('name', $categoryName)
                              ->orWhere('name', strtolower($categoryName))
                              ->orWhere('name', strtoupper($categoryName));
                        })
                        ->first();
                    if (!$category) {
                        $category = Category::create([
                            'name'      => $categoryName,
                            'branch_id' => $branchId,
                            'isDeleted' => 0
                        ]);
                    }

                    // Brand
                    $brand = $brandName ? Brand::firstOrCreate(
                        ['name' => strtolower($brandName), 'branch_id' => $branchId],
                        ['isDeleted' => 0, 'status' => 'active']
                    ) : null;

                    $unit = $unitName ? Unit::firstOrCreate(
                        ['unit_name' => strtolower($unitName), 'created_by' => $branchId],
                        ['isDeleted' => 0]
                    ) : null;

                    // Check if product already exists by product_code/SKU or Name + Category
                    $product = null;
                    if (!empty($sku)) {
                        $product = Product::where(function($q) use ($sku) {
                                $q->where('product_code', $sku)->orWhere('SKU', $sku);
                            })
                            ->where('branch_id', $branchId)
                            ->where('isDeleted', 0)
                            ->first();
                    }
                    if (!$product) {
                        $product = Product::where('name', $name)->where('category_id', $category->id)->where('branch_id', $branchId)->where('isDeleted', 0)->first();
                    }

                    if (empty($sku)) {
                        if ($product) {
                            $sku = $product->SKU ?: ($product->product_code ?: 'ITEM' . $product->id);
                        } else {
                            do {
                                $tempSku = 'SKU' . mt_rand(10000000, 99999999);
                            } while (Product::where('SKU', $tempSku)->where('branch_id', $branchId)->exists());
                            $sku = $tempSku;
                        }
                    }

                    $isImeiManaged = ($maintainImei === 'y') || $this->isMobileCategoryId($category->id);
                    $imeisList = [];
                    if ($isImeiManaged && !empty($autoImei) && strtolower($autoImei) !== 'n' && $autoImei !== '-') {
                        $splitImeis = preg_split('/[\n\r,;\/]+/', $autoImei);
                        foreach ($splitImeis as $splitImei) {
                            $cleanImei = trim($splitImei);
                            if ($cleanImei !== '' && strtolower($cleanImei) !== 'n') {
                                $imeisList[] = $cleanImei;
                            }
                        }
                    }

                    if ($product) {
                        $oldQty = (float) $product->quantity;
                        $product->quantity += $quantity;
                        if ($brand && !$product->brand_id) {
                            $product->brand_id = $brand->id;
                        }
                        if ($isImeiManaged) {
                            $existingImeis = json_decode($product->imei_no ?? '[]', true);
                            if (!is_array($existingImeis)) {
                                $existingImeis = [];
                            }
                            foreach ($imeisList as $imei) {
                                if (!in_array($imei, $existingImeis)) {
                                    $existingImeis[] = $imei;
                                }
                            }
                            $product->imei_no = json_encode($existingImeis);
                        }
                        $product->availablility = $product->quantity > 0 ? 'in_stock' : 'out_stock';
                        $product->save();

                        ProductInventory::create([
                            'product_id'    => $product->id,
                            'initial_stock' => $oldQty,
                            'current_stock' => $product->quantity,
                            'branch_id'     => $branchId,
                            'create_by'     => $user->id,
                            'type'          => 'Edit',
                            'date'          => now(),
                        ]);

                        $updatedSKUs[] = $sku;
                    } else {
                        $product = Product::create([
                            'name'          => $name,
                            'category_id'   => $category->id,
                            'brand_id'      => $brand?->id,
                            'SKU'           => $sku,
                            'product_code'  => $sku,
                            'quantity'      => $quantity,
                            'unit_id'       => $unit?->id,
                            'price'         => $price,
                            'hsn_code'      => $hsnCode,
                            'status'        => $status,
                            'availablility' => $quantity > 0 ? 'in_stock' : 'out_stock',
                            'description'   => $description,
                            'branch_id'     => $branchId,
                            'imei_no'       => json_encode($imeisList),
                            'create_by'     => $user->id,
                        ]);

                        ProductInventory::create([
                            'product_id'    => $product->id,
                            'initial_stock' => $quantity,
                            'current_stock' => $quantity,
                            'branch_id'     => $branchId,
                            'create_by'     => $user->id,
                            'type'          => 'Create',
                            'date'          => now(),
                        ]);

                        $insertedCount++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'CSV Import failed: ' . $e->getMessage()]);
            }
        }

        if ($insertedCount > 0 || count($updatedSKUs) > 0) {
            return response()->json([
                'status'       => true,
                'message'      => $insertedCount > 0
                    ? "{$insertedCount} product(s) imported successfully."
                    : 'Existing product(s) updated.',
                'updated_skus' => $updatedSKUs,
                'invalid_skus' => $invalidSKUs,
                'skipped_rows' => $skippedRows,
            ]);
        }

        if (!empty($invalidSKUs)) {
            return response()->json([
                'status'       => false,
                'message'      => 'No new products imported. SKU must contain numeric digits only.',
                'invalid_skus' => $invalidSKUs,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'File is empty or contains no valid product data.',
        ]);
    }

    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'brand', 'unit'])->findOrFail($id);

        $basePath = rtrim((string) (env('IMAGE_PATH') ?? env('ImagePath') ?? env('APP_URL', url('/'))), '/');
        $rawImages = json_decode($product->images ?? '[]', true);
        $rawImages = is_array($rawImages) ? $rawImages : [];
        $imageUrls = [];

        foreach ($rawImages as $image) {
            if (!is_string($image) || trim($image) === '') {
                continue;
            }

            $normalizedImage = ltrim($image, '/');

            if (str_starts_with($normalizedImage, 'http://') || str_starts_with($normalizedImage, 'https://')) {
                $imageUrls[] = $normalizedImage;
            } elseif (str_starts_with($normalizedImage, 'admin/assets/')) {
                $imageUrls[] = $basePath . '/' . $normalizedImage;
            } else {
                $imageUrls[] = $basePath . '/storage/' . $normalizedImage;
            }
        }

        if (empty($imageUrls)) {
            $imageUrls[] = $basePath . '/admin/assets/img/product/noimage.png';
        }

        // Fetch currency settings
        $settings         = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? 'â‚¹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $barcodeGenerator = new DNS1D();
        $barcodeHtml      = null;

        if (! empty($product->barcode)) {
            $barcodeHtml = $barcodeGenerator->getBarcodeHTML($product->barcode, 'C128');
        }

        return response()->json([
            'product'          => $product,
            'image_urls'       => $imageUrls,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'barcode_html'     => $barcodeHtml,
        ]);
    }

    public function getQuantityHistory($productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return response()->json(['status' => false, 'message' => 'Product not found.']);
        }

        $history = [];

        // Initial stock (e.g., when product added)
        $history[] = [
            'type'     => 'Added',
            'quantity' => $product->quantity,
            'note'     => 'Initial stock on product creation',
            'date'     => $product->created_at->format('d-M-Y h:i A'),
        ];

        // Sales data (reduce quantity)
        $orderItems = OrderItem::with('order')->where('product_id', $productId)->get();
        foreach ($orderItems as $item) {
            $orderNumber = $item->order ? $item->order->order_number : 'N/A';

            $history[] = [
                'type'     => 'Sold',
                'quantity' => $item->quantity,
                'note'     => 'Sold in order #' . $orderNumber,
                'date'     => $item->created_at->format('d-M-Y h:i A'),
            ];
        }

        return response()->json([
            'status'  => true,
            'product' => $product->name,
            'history' => $history,
        ]);
    }

    public function getCategory(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Agar request me sub_branch_id aaya hai to use karo, warna logged-in user ka branch_id lo
        $branchId = $request->sub_branch_id ?? $user->id;
        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }
        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        // dd($categories);

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ], 200);
    }

    public function getBrand(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Branch ID decide karna (agar sub_branch_id aaya hai to use karo, otherwise user ka branch_id)
        $branchId = $request->sub_branch_id ?? $user->id;

        // Categories fetch
        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }
        $Brands = Brand::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $Brands,
        ], 200);
    }

    public function getTaxRates(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = $request->sub_branch_id ?? $user->id;

        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }

        $taxRates = TaxRate::where('isDeleted', 0)
            ->where('status', 'active')
            ->where('branch_id', $branchId)
            ->get(['id', 'tax_name', 'tax_rate']);

        return response()->json([
            'status' => true,
            'data'   => $taxRates,
        ], 200);
    }

    public function getUnits(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = $request->sub_branch_id ?? $user->id;

        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }

        $units = Unit::where('is_delete', 0)
            ->where('created_by', $branchId)
            ->get(['id', 'unit_name']);

        return response()->json([
            'status' => true,
            'data'   => $units,
        ], 200);
    }

    public function edit_product(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        // dd($product);
        // $units = Unit::find($product->unit_id);

        // dd($units);
        return response()->json([
            'status'  => true,
            'product' => $product,
            // 'units'    => $units,
        ]);
    }
    private function resolveProfitLossDateRange($timePeriod)
    {
        return match ($timePeriod) {
            'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'last_6_months' => [now()->subMonths(5)->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'previous_year' => [now()->subYear()->startOfYear()->toDateString(), now()->subYear()->endOfYear()->toDateString()],
            default => [null, null],
        };
    }

    private function applyDateFilters($query, $column, $startDate = null, $endDate = null, $year = null, $month = null)
    {
        if ($startDate) {
            $query->whereDate($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($column, '<=', $endDate);
        }

        if ($year) {
            $query->whereYear($column, $year);
        }

        if ($month) {
            $query->whereMonth($column, $month);
        }

        return $query;
    }

    private function getProfitLossReportData($branchId, $productId = null, $vendorId = null, $customerId = null, $startDate = null, $endDate = null, $year = null, $month = null, $timePeriod = 'all_time', ?User $user = null)
    {
        $user = $user ?? Auth::user();
        $allowedProductIds = null;

        if ($productId) {
            $allowedProductIds = [(int) $productId];
        }

        if ($vendorId) {
            $vendorProductIds = DB::table('purchases')
                ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
                ->where('purchases.isDeleted', 0)
                ->where('purchase_invoice.isDeleted', 0)
                ->where('purchase_invoice.branch_id', $branchId)
                ->where('purchase_invoice.vendor_id', $vendorId);

            if ($user) {
                StaffDepartmentScope::applyRestrictedPurchaseJoinScope($vendorProductIds, $user);
            }

            $this->applyDateFilters(
                $vendorProductIds,
                DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at)'),
                $startDate,
                $endDate,
                $year,
                $month
            );

            $vendorProductIds = $vendorProductIds->distinct()->pluck('purchases.item')->map(fn($id) => (int) $id)->all();
            $allowedProductIds = is_array($allowedProductIds)
                ? array_values(array_intersect($allowedProductIds, $vendorProductIds))
                : $vendorProductIds;
        }

        if ($customerId) {
            $customerProductIds = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.isDeleted', 0)
                ->where('products.isDeleted', 0)
                ->where('orders.payment_status', 'completed')
                ->where('products.branch_id', $branchId)
                ->where('orders.user_id', $customerId);

            if ($user) {
                StaffDepartmentScope::applyRestrictedOrderJoinScope($customerProductIds, $user);
            }

            $this->applyDateFilters($customerProductIds, 'orders.created_at', $startDate, $endDate, $year, $month);

            $customerProductIds = $customerProductIds->distinct()->pluck('products.id')->map(fn($id) => (int) $id)->all();
            $allowedProductIds = is_array($allowedProductIds)
                ? array_values(array_intersect($allowedProductIds, $customerProductIds))
                : $customerProductIds;
        }

        $salesQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.isDeleted', 0)
            ->where('products.isDeleted', 0)
            ->where('orders.payment_status', 'completed')
            ->where('products.branch_id', $branchId)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'order_items.quantity',
                'order_items.total_amount as sales_amount',
                'orders.created_at as transaction_date'
            );

        if ($user) {
            StaffDepartmentScope::applyRestrictedOrderJoinScope($salesQuery, $user);
        }

        if ($customerId) {
            $salesQuery->where('orders.user_id', $customerId);
        }

        if (is_array($allowedProductIds)) {
            if (empty($allowedProductIds)) {
                $salesQuery->whereRaw('1 = 0');
            } else {
                $salesQuery->whereIn('products.id', $allowedProductIds);
            }
        }

        $this->applyDateFilters($salesQuery, 'orders.created_at', $startDate, $endDate, $year, $month);
        $salesRows = $salesQuery->get();

        $purchaseQuery = DB::table('purchases')
            ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
            ->join('products', 'purchases.item', '=', 'products.id')
            ->where('purchases.isDeleted', 0)
            ->where('purchase_invoice.isDeleted', 0)
            ->where('products.isDeleted', 0)
            ->where('purchase_invoice.branch_id', $branchId)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'purchases.quantity',
                'purchases.price as purchase_rate',
                DB::raw('COALESCE(NULLIF(purchases.amount_total, 0), purchases.price * purchases.quantity) as purchase_amount'),
                DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at) as transaction_date')
            );

        if ($user) {
            StaffDepartmentScope::applyRestrictedPurchaseJoinScope($purchaseQuery, $user);
        }

        if ($vendorId) {
            $purchaseQuery->where('purchase_invoice.vendor_id', $vendorId);
        }

        if (is_array($allowedProductIds)) {
            if (empty($allowedProductIds)) {
                $purchaseQuery->whereRaw('1 = 0');
            } else {
                $purchaseQuery->whereIn('products.id', $allowedProductIds);
            }
        }

        $this->applyDateFilters(
            $purchaseQuery,
            DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at)'),
            $startDate,
            $endDate,
            $year,
            $month
        );

        $purchaseRows = $purchaseQuery->get();

        $productBreakdown = [];
        $periodProfit = [];
        $rowMap = [];
        $dayMode = (! empty($year) && ! empty($month)) || in_array($timePeriod, ['this_week', 'this_month'], true);

        foreach ($salesRows as $row) {
            $productKey = (string) $row->product_id;
            $salesAmount = (float) $row->sales_amount;
            $quantity = (float) $row->quantity;

            if (! isset($productBreakdown[$productKey])) {
                $productBreakdown[$productKey] = [
                    'product_id' => (int) $row->product_id,
                    'name' => $row->product_name,
                    'sales' => 0.0,
                    'purchase' => 0.0,
                    'profit' => 0.0,
                    'sales_qty' => 0.0,
                    'purchase_qty' => 0.0,
                    'latest_date' => $row->transaction_date,
                ];
            }

            $productBreakdown[$productKey]['sales'] += $salesAmount;
            $productBreakdown[$productKey]['sales_qty'] += $quantity;
            $productBreakdown[$productKey]['latest_date'] = max($productBreakdown[$productKey]['latest_date'], $row->transaction_date);

            $dt = \Carbon\Carbon::parse($row->transaction_date);
            $periodKey = $dayMode ? $dt->format('Y-m-d') : $dt->format('Y-m');
            $periodLabel = $dayMode ? $dt->format('d M Y') : $dt->format('M Y');

            if (! isset($periodProfit[$periodKey])) {
                $periodProfit[$periodKey] = ['label' => $periodLabel, 'profit' => 0.0];
            }
            $periodProfit[$periodKey]['profit'] += $salesAmount;

            $rowMap[$productKey] = true;
        }

        foreach ($purchaseRows as $row) {
            $productKey = (string) $row->product_id;
            $purchaseAmount = (float) $row->purchase_amount;
            $purchaseRate = (float) $row->purchase_rate;
            $quantity = (float) $row->quantity;

            if (! isset($productBreakdown[$productKey])) {
                $productBreakdown[$productKey] = [
                    'product_id' => (int) $row->product_id,
                    'name' => $row->product_name,
                    'sales' => 0.0,
                    'purchase' => 0.0,
                    'profit' => 0.0,
                    'sales_qty' => 0.0,
                    'purchase_qty' => 0.0,
                    'latest_date' => $row->transaction_date,
                ];
            }

            $productBreakdown[$productKey]['purchase'] += $purchaseAmount;
            $productBreakdown[$productKey]['purchase_qty'] += $quantity;
            $productBreakdown[$productKey]['latest_date'] = max($productBreakdown[$productKey]['latest_date'], $row->transaction_date);

            $dt = \Carbon\Carbon::parse($row->transaction_date);
            $periodKey = $dayMode ? $dt->format('Y-m-d') : $dt->format('Y-m');
            $periodLabel = $dayMode ? $dt->format('d M Y') : $dt->format('M Y');

            if (! isset($periodProfit[$periodKey])) {
                $periodProfit[$periodKey] = ['label' => $periodLabel, 'profit' => 0.0];
            }
            $periodProfit[$periodKey]['profit'] -= $purchaseAmount;

            if (! isset($rowMap[$productKey])) {
                $rowMap[$productKey] = true;
            }
        }

        foreach ($productBreakdown as &$item) {
            $item['profit'] = $item['sales'] - $item['purchase'];
            $item['purchase_rate'] = $item['purchase_qty'] > 0
                ? $item['purchase'] / $item['purchase_qty']
                : 0.0;
            unset($item['sales_qty'], $item['purchase_qty']);
        }
        unset($item);

        usort($productBreakdown, fn($a, $b) => strcmp($a['name'], $b['name']));

        ksort($periodProfit);
        $labels = array_values(array_map(fn($item) => $item['label'], $periodProfit));
        $profitValues = array_values(array_map(fn($item) => round($item['profit'], 2), $periodProfit));

        $totalSales = round(array_sum(array_column($productBreakdown, 'sales')), 2);
        $totalPurchase = round(array_sum(array_column($productBreakdown, 'purchase')), 2);
        $totalProfitAmount = round(array_sum(array_map(fn($item) => $item['profit'] > 0 ? $item['profit'] : 0, $productBreakdown)), 2);
        $totalLossAmount = round(array_sum(array_map(fn($item) => $item['profit'] < 0 ? abs($item['profit']) : 0, $productBreakdown)), 2);

        $pdfItems = collect($productBreakdown)->map(function ($item, $index) {
            return (object) [
                'created_at' => $item['latest_date'],
                'product_name' => $item['name'],
                'quantity' => 1,
                'purchase_rate' => $item['purchase'],
                'sales_amount' => $item['sales'],
                'profit_amount' => $item['profit'],
            ];
        })->values();

        return [
            'total_sales' => $totalSales,
            'total_purchase' => $totalPurchase,
            'total_profit' => round($totalSales - $totalPurchase, 2),
            'total_profit_amount' => $totalProfitAmount,
            'total_loss_amount' => $totalLossAmount,
            'has_data' => ! empty($productBreakdown),
            'product_breakdown' => $productBreakdown,
            'chart' => [
                'labels' => ! empty($labels) ? $labels : [now()->format($dayMode ? 'd M Y' : 'M Y')],
                'datasets' => [
                    [
                        'label' => 'Total Profit / Loss',
                        'data' => ! empty($profitValues) ? $profitValues : [0],
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 6,
                        'pointBackgroundColor' => '#007bff',
                        'cubicInterpolationMode' => 'monotone',
                    ],
                ],
            ],
            'pdf_items' => $pdfItems,
        ];
    }

    private function resolveBranchId()
    {
        $user       = auth()->user();
        $role       = strtolower($user->role ?? '');
        $subAdminId = session('selectedSubAdminId');

        return match ($role) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $subAdminId ?: $user->id,
            default     => $user->id,
        };
    }


    public function getProfitLossData(Request $request)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        $branchId = $this->resolveBranchId();

        $productId = $request->get('product_id');
        $vendorId = $request->get('vendor_id');
        $customerId = $request->get('customer_id');
        $timePeriod = $request->get('time_period', 'all_time');
        $year = $request->get('year');
        $month = $request->get('month');

        [$startDate, $endDate] = $this->resolveProfitLossDateRange($timePeriod);

        return response()->json(
            $this->getProfitLossReportData($branchId, $productId, $vendorId, $customerId, $startDate, $endDate, $year, $month, $timePeriod, $user)
        );
    }

    public function profitLossPdf(Request $request)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        $branchId = $this->resolveBranchId();

        $productId = $request->get('product_id');
        $vendorId = $request->get('vendor_id');
        $customerId = $request->get('customer_id');
        $timePeriod = $request->get('time_period', 'all_time');
        $year = $request->get('year');
        $month = $request->get('month');

        [$startDate, $endDate] = $this->resolveProfitLossDateRange($timePeriod);
        $reportData = $this->getProfitLossReportData($branchId, $productId, $vendorId, $customerId, $startDate, $endDate, $year, $month, $timePeriod, $user);
        $items = $reportData['pdf_items'];

        $settings = DB::table('settings')->where('branch_id', $branchId)->first();
        $selectedProduct = $productId ? Product::find($productId) : null;
        $selectedVendor = $vendorId ? User::find($vendorId) : null;
        $selectedCustomer = $customerId ? User::find($customerId) : null;

        $pdf = Pdf::loadView('reports.profit-loss-report-pdf', [
            'items' => $items,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'year' => $year,
            'month' => $month,
            'settings' => $settings,
            'selectedProduct' => $selectedProduct,
            'selectedVendor' => $selectedVendor,
            'selectedCustomer' => $selectedCustomer,
        ]);

        return $pdf->download('Profit_Loss_Report.pdf');
    }
}
