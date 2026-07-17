<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'plan_id',
        'name',
        'company_name',
        'membership_no',
        'customer_since',
        'email',
        'phone',
        'alternate_phone',
        'credit_limit',
        'credit_days',
        'role',
        'state_code',
        'state_name',
        'customer_category',
        'age',
        'dob',
        'doa',
        'gst_number',
        'gstin_status',
        'gender',
        'pan_number',
        'pan_status',
        'tin_number',
        'account_group',
        'password',
        'isDeleted',
        'status',
        'profile_image',
        'face_descriptor',
        'face_image',
        'haspermission',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['profile_image_url'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'face_descriptor'   => 'array',
        'customer_since'    => 'date:Y-m-d',
        'dob'               => 'date:Y-m-d',
        'doa'               => 'date:Y-m-d',
    ];

    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'id');
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }
    public function getProfileImageUrlAttribute()
    {
        $basePath = env('ImagePath', '/'); // default "/" if not set

        if ($this->profile_image) {
            return url($basePath . 'storage/' . $this->profile_image);
        }

        // fallback image
        return url($basePath . 'admin/assets/img/customer/customer5.jpg');
    }
    // User.php
    public function permissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
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
