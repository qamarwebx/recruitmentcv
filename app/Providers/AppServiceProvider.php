<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Domain;
use App\Services\Backup\Contracts\DumpRunner;
use App\Services\Backup\Contracts\DriveTokenRefresher;
use App\Services\Backup\Contracts\DriveUploader;
use App\Services\Backup\GoogleApiDriveTokenRefresher;
use App\Services\Backup\GoogleApiDriveUploader;
use App\Services\Backup\MysqldumpRunner;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DumpRunner::class, MysqldumpRunner::class);
        $this->app->bind(DriveUploader::class, GoogleApiDriveUploader::class);
        $this->app->bind(DriveTokenRefresher::class, GoogleApiDriveTokenRefresher::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'lead' => \App\Models\Lead::class,
            'deal' => \App\Models\DealPipeline::class,
        ]);

        // Default mailer = the website's SMTPs with sequential failover.
        \App\Support\SmtpMailer::register();

        try {
            $domain = str_replace('www.', '', request()->getHost());
    
            $record = Domain::where('domain_name', $domain)->first();
    
            // Default values
            $website = [
                'logo' => '',
                'logo_ar' => '',
                'company_name' => '',
                'company_name_ar' => '',
                'company_address' => '',
                'company_address_ar' => '',
                'company_mobile' => '',
                'company_email' => '',
            ];
    
            if ($record) {
    
                $website['logo'] = $record->website_logo 
                    ? asset('admin/assets/images/partner/'.$record->website_logo)
                    : '';
    
                $website['logo_ar'] = $record->website_logo_ar 
                    ? asset('admin/assets/images/partner/'.$record->website_logo_ar)
                    : '';
    
                // ✅ NEW FIELDS
                $website['company_name'] = $record->company_name ?? '';
                $website['company_name_ar'] = $record->company_name_ar ?? '';
    
                $website['company_address'] = $record->company_address ?? '';
                $website['company_address_ar'] = $record->company_address_ar ?? '';
                $website['company_mobile'] = $record->company_mobile ?? '';
                $website['company_email'] = $record->company_email ?? '';
            }
    
            // 🔥 Share globally
            \Illuminate\Support\Facades\View::share('website', $website);
    
        } catch (\Exception $e) {
    
            \Illuminate\Support\Facades\View::share('website', [
                'logo' => '',
                'logo_ar' => '',
                'company_name' => '',
                'company_name_ar' => '',
                'company_address' => '',
                'company_address_ar' => '',
                'company_mobile' => '',
                'company_email' => '',
            ]);
        }
    }
}
