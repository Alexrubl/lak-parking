<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\History;
use App\Models\Tenant;
use App\Models\Controller;
use App\Models\TypeTransport;
use App\Models\Rate;
use App\Policies\TenantPolicy;
use App\Policies\HistoryPolicy;
use App\Policies\AdminPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        History::class => HistoryPolicy::class,
        Tenant::class => TenantPolicy::class,
        Controller::class => AdminPolicy::class,
        TypeTransport::class => AdminPolicy::class,
        Rate::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
