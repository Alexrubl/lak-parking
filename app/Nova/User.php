<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\MorphToMany;
use Vyuldashev\NovaPermission\PermissionBooleanGroup;
use Vyuldashev\NovaPermission\RoleBooleanGroup;
use Vyuldashev\NovaPermission\RoleSelect;
use Laravel\Nova\Fields\BelongsToMany;

class User extends Resource
{    
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

    public static $priority = 1;

    public static function group()
    {
        return __('nova-permission-tool::navigation.sidebar-label');
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label() {
        return 'Пользователи';
    }

    public static function singularlabel() {
        return 'Пользователя';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->isAdmin()) {
            $query->where('name', $request->user()->name);
        }
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Имя', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Эл.почта', 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Пароль', 'Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
            
            BelongsToMany::make('Арендатор', 'tenant', 'App\Nova\Tenant')->canSee(fn () => $request->user()->isAdmin()),

            RoleSelect::make('Роль', 'roles')->canSee(fn () => $request->user()->isAdmin()),
            
            // MorphToMany::make('Roles', 'roles', \Vyuldashev\NovaPermission\Role::class)->canSee(fn () => $request->user()->isAdmin()),
            // MorphToMany::make('Permissions', 'permissions', \Vyuldashev\NovaPermission\Permission::class)->canSee(fn () => $request->user()->isAdmin()),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    // public static function authorizable() {
    //     return true;
    // }

}
