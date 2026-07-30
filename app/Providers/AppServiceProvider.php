<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Setting;
use App\Services\StaffPermission;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Backup when config is already loaded (env() is empty after config:cache).
        if (config('database.connections.mysql.host') === '127.0.0.1' && ! in_array(PHP_OS_FAMILY, ['Windows', 'Darwin'], true)) {
            config(['database.connections.mysql.host' => 'localhost']);
        }

        // After config:cache, env() is empty in providers — use config (filled from .env at cache time).
        $imagePath = config('app.image_path');
        if (! is_string($imagePath) || $imagePath === '') {
            $raw = config('app.url', 'http://localhost');
            $imagePath = rtrim((string) $raw, '/').'/';
            config(['app.image_path' => $imagePath]);
        }

        // Legacy Blade env('ImagePath') — must match config when .env is not read at runtime.
        $_ENV['ImagePath'] = $imagePath;
        $_ENV['IMAGE_PATH'] = $imagePath;
        putenv('ImagePath='.$imagePath);
        putenv('IMAGE_PATH='.$imagePath);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::share('imagePath', config('app.image_path'));
        // Guard against fresh-database installs where `settings` table doesn't exist yet.
        // Without this, `php artisan migrate` crashes before it can even create the table.
        try {
            $appSetting = Setting::first() ?? new Setting([
                'name'              => 'ERP Inventory System',
                'email'             => 'info@gmail.com',
                'phone'             => 1234567890,
                'address'           => 'Adajan Surat',
                'logo'              => 'admin/assets/img/logo-image.jpg',
                'currency_symbol'   => '₹',
                'currency_position' => 'left',
            ]);
        } catch (\Exception $e) {
            $appSetting = new Setting([
                'name'              => 'ERP Inventory System',
                'email'             => 'info@gmail.com',
                'phone'             => 1234567890,
                'address'           => 'Adajan Surat',
                'logo'              => 'admin/assets/img/logo-image.jpg',
                'currency_symbol'   => '₹',
                'currency_position' => 'left',
            ]);
        }
        \Illuminate\Support\Facades\View::share('appSetting', $appSetting);

        // Do not enable Passport::hashClientSecrets() unless PASSPORT_PERSONAL_ACCESS_CLIENT_ID
        // and PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET are set in .env (see README).

        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');      
            } catch (\Exception $e) {
                // Optional: Log error or ignore silently
            }
        }
        app()->singleton('hasPermission', function () {
            return static function ($moduleId, $action) {
                return StaffPermission::check((int) $moduleId, (string) $action);
            };
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $user = Auth::user();
            $view->with('staffPermissionFullAccess', StaffPermission::hasFullAccess($user));
            $view->with('staffPermissionMap', StaffPermission::allModuleFlags($user));
        });
    }
}
