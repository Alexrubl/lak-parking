<?php

namespace App\Providers;

use App\Models\Log;
use App\Models\Tenant;
use App\Models\Transport;
use App\Observers\LogObserver;
use App\Observers\TenantObserver;
use App\Observers\TransportObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Регистрация любых служб приложения.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transport::observe(TransportObserver::class);
        Tenant::observe(TenantObserver::class);
        Log::observe(LogObserver::class);

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }
}
