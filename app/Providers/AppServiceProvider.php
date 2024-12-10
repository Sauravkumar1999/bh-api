<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!defined('LOCALE_ENUM')) {
            define('LOCALE_ENUM', config('app.available_locales'));
        }
        if(App::environment() !== "local"){
            URL::forceScheme("https");
        }
    }
}
