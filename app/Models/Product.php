<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'brand_id',
        'branch_id',
        'name',
        'SKU',
        'hsn_code',
        'barcode',
        'description',
        'price',
        'cost_price',
        'landing_cost',
        'product_code',
        'supplier_code',
        'international_code',
        'serial_no_status',
        'non_inventory_type',
        'stock_validation_status',
        'item_type',
        'discount_print_status',
        'item_created_on',
        'images',
        'quantity',
        'imei_no',
        'unit_id',
        'isDeleted',
        'availablility',
        'status',
        'gst_option',
        'product_gst',
        'created_at',
        'updated_at',
        'create_by',
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Relationship with Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // protected static function booted()
    // {
    //     static::creating(function ($product) {
    //         do {
    //             $barcode = 'PRD' . mt_rand(1000000000, 9999999999); // 13-char barcode
    //         } while (Product::where('barcode', $barcode)->exists());

    //         $product->barcode = $barcode;
    //     });
    // }
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->barcode)) {
                do {
                    $barcode = 'PRD' . mt_rand(1000000000, 9999999999);
                } while (Product::where('barcode', $barcode)->exists());

                $product->barcode = $barcode;
            }
        });
    }
    public function getImageUrlAttribute()
    {
        $images = $this->images;

        if ($images) {
            $decoded = json_decode($images, true);

            if (is_array($decoded)) {
                return array_map(function ($img) {
                    return image_path('storage/' . ltrim($img, '/'));
                }, array_filter($decoded));
            }

            return [image_path('storage/' . ltrim($images, '/'))];
        }

        return [image_path('admin/assets/img/product/noimage.png')];
    }

    public function product_inventory()
    {
        return $this->hasOne(ProductInventory::class, 'product_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Kolkata');
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });
    }
}
