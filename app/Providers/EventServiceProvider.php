<?php

namespace App\Providers;

use App\Events\CreateByExcel;
use App\Events\Register;
use App\Events\ResetPassword;
use App\Listeners\CreateProductNotification;
use App\Listeners\RegisterNotification;
use App\Listeners\ResetPasswordNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Register::class => [RegisterNotification::class],
        ResetPassword::class => [

            ResetPasswordNotification::class
        ],
        CreateByExcel::class => [
            CreateProductNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
