<?php

namespace App\Providers;

use App\Events\ChannelCreated;
use App\Events\ProductCreated;
use App\Listeners\IncreaseNextChannelCode;
use App\Listeners\IncreaseNextProductCode;
use App\Events\UserCreated;
use App\Events\UserLoggedOut;
use App\Events\UserUpdated;
use App\Listeners\LogoutMobileUser;
use App\Listeners\SaveRecommender;
use App\Listeners\SetDefaultUserPermissions;
use App\Listeners\UserConfirmation;
use App\Events\CompanyCreated;
use App\Listeners\IncreaseNextCompanyCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        UserCreated::class => [
            UserConfirmation::class,
            SaveRecommender::class,
            SetDefaultUserPermissions::class,

        ],
        UserUpdated::class => [
            UserConfirmation::class,
            SaveRecommender::class
        ],
        UserLoggedOut::class => [
            LogoutMobileUser::class,
        ],
        ChannelCreated::class => [
            IncreaseNextChannelCode::class,
        ],
        ProductCreated::class => [
            IncreaseNextProductCode::class,
        ],
        CompanyCreated::class => [
            IncreaseNextCompanyCode::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
