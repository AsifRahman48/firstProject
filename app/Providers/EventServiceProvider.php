<?php

namespace App\Providers;

use App\Category;
use App\Company;
use App\Observers\CategoryObserver;
use App\Observers\CompanyObserver;
use App\Observers\SubCategoryObserver;
use App\Observers\UserObserver;
use App\SubCategory;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Category::observe(CategoryObserver::class);
        Company::observe(CompanyObserver::class);
        SubCategory::observe(SubCategoryObserver::class);
        User::observe(UserObserver::class);
    }
}
