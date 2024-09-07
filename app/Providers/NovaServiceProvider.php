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
use Illuminate\Http\Request;
use App\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use App\Nova\Tenant;
use App\Nova\Transport;
use App\Nova\History;
use App\Nova\Log;
use App\Nova\User;
use App\Nova\TypeTransport;
use App\Nova\Rate;
use App\Nova\Controller;
use Alexrubl\NovaPermission\Role;
use Alexrubl\NovaPermission\Permission;
use Laravel\Nova\Http\Controllers\LoginController;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::sortResourcesBy(function ($resource) {
            return $resource::$priority ?? 9999;
        });

        //if (Auth::user()) {
           //Nova::initialPath('/resources/transports');
        //}

        //Nova::withBreadcrumbs();
        Nova::footer(function ($request) {
            return Blade::render('
                @env(\'prod\')
                    This is production!
                @endenv
            ');
        });

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar')->canSee(fn ($request) => $request->user()->isAdmin() || $request->user()->isSecurity()),
                MenuSection::make('Справочники', [
                    MenuItem::resource(Tenant::class),
                    MenuItem::resource(Transport::class),
                    MenuItem::resource(TypeTransport::class),
                    MenuItem::resource(Rate::class),
                    MenuItem::resource(Controller::class),
                ])->collapsable()->icon('collection'),
                MenuSection::make('Отчёты', [
                    // MenuItem::resource(Report::class),
                    MenuItem::resource(History::class),
                    MenuItem::resource(Log::class),
                ])->collapsable()->icon('document-report'),
                MenuSection::make('Настройки ', [
                    MenuItem::make('Основные')->path('/settings/general'),
                    MenuItem::make('Эквайринг Ckassa')->path('/settings/ekvairing-ckassa'),
                    MenuItem::make('Эквайринг Ckassa')->path('/settings/uvedomleniia'),
                ])->collapsable()->icon('adjustments')->canSee(fn ($request) => $request->user()->isAdmin()),
                MenuSection::make('Учётные записи', [
                    MenuItem::resource(User::class),
                    MenuItem::resource(Role::class),
                    MenuItem::resource(Permission::class),
                ])->collapsable()->icon('user')
            ];
        });

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Text::make('api key', 'apikey'),
            Boolean::make('Открыть проезд в обход контроллера', 'openForceEntry'),
            Number::make('Кол-во проездов в кредит', 'count_credit')->default(5),
            Boolean::make('Интеграция с Сигуром', 'active_sigur_exchange'),
        ]);

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Panel::make('Тестовые настройки', [
                Boolean::make('Тестовые настройки', 'test_ckassa'),
                Text::make('ShopToken', 'test_ShopToken'),
                Text::make('secKey', 'test_secKey'),
                // Text::make('ApiLoginAuthorization', 'test_ApiLoginAuthorization'),
                // Text::make('ApiAuthorization', 'test_ApiAuthorization'),
                Text::make('servCode', 'test_servCode'),
                // Text::make('Название организации', 'test_organization')->help('Строка, мин. 1 символ - макс. 200 символов'),
                // Text::make('Идентификатор организации', 'test_identificator')->help('Целое число, мин. 1 символ - макс. 5 '),
            ]),
            Panel::make('Боевые настройки', [
                Text::make('ShopToken', 'ShopToken'),
                Text::make('secKey', 'secKey'),
                // Text::make('ApiLoginAuthorization', 'ApiLoginAuthorization'),
                // Text::make('ApiAuthorization', 'ApiAuthorization'),
                Text::make('servCode', 'servCode'),
                // Text::make('Название организации', 'organization')->help('Строка, мин. 1 символ - макс. 200 символов'),
                // Text::make('Идентификатор организации', 'identificator')->help('Целое число, мин. 1 символ - макс. 5 '),
            ]),
        ], [], 'Эквайринг Ckassa');

        \Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
            Panel::make('Электронная почта', [
                Text::make('SMTP сервер', 'smtp_server'),
                Number::make('SMTP порт', 'smtp_port')->default(465),
                Text::make('Шифрование', 'smtp_encryption'),
                Text::make('Эл.почта', 'smtp_email'),
                Text::make('Логин', 'smtp_username'),
                Text::make('Пароль', 'smtp_password')->withMeta(['type' => 'password']),
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
            \Alexrubl\NovaPermission\NovaPermissionTool::make()
                ->rolePolicy(RolePolicy::class)
                ->permissionPolicy(PermissionPolicy::class),
            \Outl1ne\NovaSettings\NovaSettings::make()->canSee(fn ($request) => $request->user()->isAdmin()),
            // (new \PhpJunior\NovaLogViewer\Tool())->canSee(function ($request) {
            //     return $request->user()->isRoot();
            // }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       $this->app->bind(LoginController::class, \App\Http\Controllers\LoginController::class);
    }
}
