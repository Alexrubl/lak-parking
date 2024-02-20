<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Transport;
use App\Models\Tenant;
use App\Models\Log;
use App\Observers\TransportObserver;
use App\Observers\TenantObserver;
use App\Observers\LogObserver;

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
        Tenant::observe(TenantObserver::class);
        Log::observe(LogObserver::class);
    }


}
