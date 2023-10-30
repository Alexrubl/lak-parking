<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Transport;
use App\Observers\TransportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {  
        Transport::observe(TransportObserver::class);
    }


}
