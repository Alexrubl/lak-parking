<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Repeater;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;
use NormanHuth\NovaRadioField\Radio;

class Rate extends Resource
{
    public static $group = '  Справочники';

    public static $priority = 3;
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Rate>
     */
    public static $model = \App\Models\Rate::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label() {
        return 'Тарифы';
    }

    public static function singularlabel() {
        return 'Тариф';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->isAdmin()) {       
            $query->where('type', '<>', 'Постоянный');
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

            Text::make('Наименование', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Radio::make('Тип', 'type')
                ->options([
                    'Разовый' => 'Разовый',
                    'Постоянный' => 'Постоянный (свой)',
                ])
                ->radioHelpTexts([
                    'Разовый' => 'при въезде',
                    'Постоянный' => 'раз в день',
                ])->inline()->rules('required'),
            
            Boolean::make('Использовать по умолчанию для Гостевого', 'default_guest')->default(0),
            
            Repeater::make('Тариф', 'items')
                ->repeatables([
                    \App\Nova\Repeater\RateItem::make(),
                ])
                ->asJson()->rules('required'),
            
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
}
