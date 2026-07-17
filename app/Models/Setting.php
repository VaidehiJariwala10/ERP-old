<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

    class Setting extends Model
    {
        use HasFactory;

        protected $fillable = [
            'branch_id',
            'name',
            'email',
            'phone',
            'state_code',
            'gst_num',
            'low_stock',
            'address',
            'logo',
            'currency_position',
            'currency_symbol',
            'bank_name',
            'branch',
            'ac_no',
            'ifsc_code',
            'qr_code',
            'favicon',
            'working_hours',
            'sunday_off',
            'saturday_off',
            'grace_period',
            'lunch_break',
            'open_time',
            'close_time',
            'invoice_size',
            'send_mail',
            'customer_whatsapp_message',
            'admin_whatsapp_message',
            'appointment_reminder_hours_before',
            'admin_whatsapp_number',
            'financial_year',
            'tds_apply',
            'show_crm_dashboard',
            'show_hr_dashboard',
            'show_erp_dashboard',
            'show_crm_lead_pipeline',
            'show_crm_conversion',
            'show_crm_followup_load',
            'show_crm_meeting_momentum',
            'show_crm_lead_status_mix',
            'show_crm_activity_trend',
            'show_crm_pipeline_quality',
            'show_crm_recent_leads',
            'show_crm_next_7_days',
            'show_hr_staff_strength',
            'show_hr_active_staff',
            'show_hr_monthly_attendance',
            'show_hr_personal_progress',
            'show_hr_attendance_pattern',
            'show_hr_salary_payroll_trend',
            'show_hr_payroll_snapshot',
            'show_hr_attendance_watch',
            'show_hr_payroll_status',
            'show_erp_total_sales',
            'show_erp_total_purchase',
            'show_erp_total_expense',
            'show_erp_sales_invoice_count',
            'show_erp_purchase_invoice_count',
            'show_erp_customers_count',
            'show_erp_vendors_count',
            'show_erp_sales_chart',
            'show_erp_purchase_chart',
            'show_erp_recent_sales',
            'show_erp_recent_purchases',
            'show_erp_recent_products',
            'show_erp_products_delivery',
            'cin_no',
            'office_latitude',
            'office_longitude',
            'office_radius',
            'location_check_enabled',
            'overtime_after_hours',
            'tax_deduction_amount',
            'salary_exceeds_amount',
            'created_at',
            'updated_at',
        ];

    protected $casts = [
        'send_mail'                  => 'boolean',
        'financial_year'             => 'boolean',
        'tds_apply'                  => 'boolean',
        'show_crm_dashboard'         => 'boolean',
        'show_hr_dashboard'          => 'boolean',
        'show_erp_dashboard'         => 'boolean',
        'show_crm_lead_pipeline'     => 'boolean',
        'show_crm_conversion'        => 'boolean',
        'show_crm_followup_load'     => 'boolean',
        'show_crm_meeting_momentum'  => 'boolean',
        'show_crm_lead_status_mix'   => 'boolean',
        'show_crm_activity_trend'    => 'boolean',
        'show_crm_pipeline_quality'  => 'boolean',
        'show_crm_recent_leads'      => 'boolean',
        'show_crm_next_7_days'       => 'boolean',
        'show_hr_staff_strength'     => 'boolean',
        'show_hr_active_staff'       => 'boolean',
        'show_hr_monthly_attendance' => 'boolean',
        'show_hr_personal_progress'  => 'boolean',
        'show_hr_attendance_pattern' => 'boolean',
        'show_hr_salary_payroll_trend' => 'boolean',
        'show_hr_payroll_snapshot'   => 'boolean',
        'show_hr_attendance_watch'   => 'boolean',
        'show_hr_payroll_status'     => 'boolean',
        'show_erp_total_sales'       => 'boolean',
        'show_erp_total_purchase'    => 'boolean',
        'show_erp_total_expense'     => 'boolean',
        'show_erp_sales_invoice_count' => 'boolean',
        'show_erp_purchase_invoice_count' => 'boolean',
        'show_erp_customers_count'   => 'boolean',
        'show_erp_vendors_count'     => 'boolean',
        'show_erp_sales_chart'       => 'boolean',
        'show_erp_purchase_chart'    => 'boolean',
        'show_erp_recent_sales'      => 'boolean',
        'show_erp_recent_purchases'  => 'boolean',
        'show_erp_recent_products'   => 'boolean',
        'show_erp_products_delivery' => 'boolean',
        'customer_whatsapp_message'  => 'boolean',
        'admin_whatsapp_message'     => 'boolean',
        'location_check_enabled'     => 'boolean',
        'office_latitude'            => 'float',
        'office_longitude'           => 'float',
        'overtime_after_hours'       => 'float',
    ];

    // Automatically append extra fields
    protected $appends = ['logo_url', 'favicon_url', 'qr_code_url'];

    // ✅ Full logo path
    public function getLogoUrlAttribute()
    {
        $basePath = env('ImagePath', '/');
        if ($this->logo) {
            return url($basePath . 'storage/' . $this->logo);
        }
        return url($basePath . 'admin/assets/img/no-logo.png'); // fallback
    }

    // ✅ Favicon URL
    public function getFaviconUrlAttribute()
    {
        $basePath = env('ImagePath', '/');
        if ($this->favicon) {
            return url($basePath . 'storage/' . $this->favicon);
        }
        return url($basePath . 'admin/assets/img/no-favicon.png'); // fallback
    }

    public function getQrCodeUrlAttribute()
    {
        $basePath = env('ImagePath', '/');
        if ($this->qr_code) {
            return url($basePath . 'storage/' . $this->qr_code);
        }
        return url($basePath . 'admin/assets/img/no-favicon.png'); // fallback
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
