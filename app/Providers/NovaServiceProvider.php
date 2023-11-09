<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Password;
use Illuminate\Support\Facades\Blade;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::sortResourcesBy(function ($resource) {
            return $resource::$priority ?? 9999;
        });

        parent::boot();
        //Nova::withBreadcrumbs();
        Nova::footer(function ($request) {
            return Blade::render('
                @env(\'prod\')
                    This is production!
                @endenv
            ');
        });

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Text::make('api key', 'apikey'),
            //Number::make('A number', 'a_number'),
            //Boolean::make('A number', 'a_number'),
        ]);

        // \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
        //     Panel::make('Эквайринг Ckassa', [
        //         Boolean::make('Тестовые настройки', 'test_ckassa'),
        //         Text::make('тестовый ApiLoginAuthorization', 'test_ApiLoginAuthorization'),
        //         Text::make('тестовый ApiAuthorization', 'test_ApiAuthorization'),
        //     ]),
        // ]);

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Panel::make('Тестовые настройки', [
                Boolean::make('Тестовые настройки', 'test_ckassa'),
                Text::make('ApiLoginAuthorization', 'test_ApiLoginAuthorization'),
                Text::make('ApiAuthorization', 'test_ApiAuthorization'),
                Text::make('servCode', 'test_servCode'),
                Text::make('Название организации', 'test_organization')->help('Строка, мин. 1 символ - макс. 200 символов'),
                Text::make('Идентификатор организации', 'test_identificator')->help('Целое число, мин. 1 символ - макс. 5 '),
            ]),
            Panel::make('Боевые настройки', [
                Text::make('ApiLoginAuthorization', 'ApiLoginAuthorization'),
                Text::make('ApiAuthorization', 'ApiAuthorization'),
                Text::make('servCode', 'servCode'),
                Text::make('Название организации', 'organization')->help('Строка, мин. 1 символ - макс. 200 символов'),
                Text::make('Идентификатор организации', 'identificator')->help('Целое число, мин. 1 символ - макс. 5 '),
            ]),
        ], [], 'Эквайринг Ckassa');

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Panel::make('Электронная почта', [
                Text::make('SMTP сервер', 'smtp_server'),
                Text::make('Логин', 'email_login'),
                Password::make('Пароль', 'email_password')
            ])
        ], [], 'Уведомления');
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'alexrubl@mail.ru'
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            \Vyuldashev\NovaPermission\NovaPermissionTool::make()
                ->rolePolicy(RolePolicy::class)
                ->permissionPolicy(PermissionPolicy::class),            
            \Outl1ne\NovaSettings\NovaSettings::make()->canSee(fn () => Auth::user()->isAdmin()),
            (new \PhpJunior\NovaLogViewer\Tool())->canSee(function ($request) {
                return Auth::user()->isAdmin();
            }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
