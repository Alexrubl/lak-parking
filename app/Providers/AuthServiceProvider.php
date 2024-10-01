<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Controller;
use App\Models\History;
use App\Models\Log;
use App\Models\Journal;
use App\Models\Rate;
use App\Models\Tenant;
use App\Models\Transport;
use App\Models\TypeTransport;
use App\Policies\AdminPolicy;
use App\Policies\HistoryPolicy;
use App\Policies\LogPolicy;
use App\Policies\TenantPolicy;
use App\Policies\TransportPolicy;
use App\Policies\UserPolicy;
use App\Policies\JournalPolicy;
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
        Log::class => LogPolicy::class,
        TypeTransport::class => AdminPolicy::class,
        Transport::class => TransportPolicy::class,
        Rate::class => AdminPolicy::class,
        User::class => UserPolicy::class,
        Journal::class => JournalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {}
}
