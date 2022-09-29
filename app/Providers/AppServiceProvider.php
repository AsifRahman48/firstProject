<?php

namespace App\Providers;

use App\Contracts\ILdapService;
use App\Contracts\ILdapUserImportLogService;
use App\Contracts\ISchedulerFullBackupService;
use App\Contracts\ISchedulerOnlyDbBackupService;
use App\Contracts\IUserService;
use App\Models\V2\Setting;
use App\Services\V2\LdapService;
use App\Services\V2\LdapUserImportLogService;
use App\Services\V2\SchedulerFullBackupService;
use App\Services\V2\SchedulerOnlyDbBackupService;
use App\Services\V2\UserService;
use App\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(Schema::hasTable('site_settings')) {
            $settings = SiteSetting::first() ?? [];
            if ($settings) {
                Config::set('app.name', $settings->site_title);
                Config::set('copyright.text', $settings->footer_copyright);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ISchedulerFullBackupService::class, SchedulerFullBackupService::class);
        $this->app->singleton(ISchedulerOnlyDbBackupService::class, SchedulerOnlyDbBackupService::class);
        $this->app->singleton(IUserService::class, UserService::class);
        $this->app->singleton(ILdapService::class, LdapService::class);
        $this->app->singleton(ILdapUserImportLogService::class, LdapUserImportLogService::class);
    }
}
