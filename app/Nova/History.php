<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ganyicz\NovaCallbacks\HasCallbacks;

class History extends Resource
{
    public static $group = ' Отчеты';
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Transaction>
     */
    public static $model = \App\Models\History::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    public static function label() {
        return 'История';
    }

    public static function singularlabel() {
        return 'История';
    }

    public $withoutActionEvents = true;

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
            $tenant_id = array();
            foreach ($request->user()->tenant as $key => $value) {
                $tenant_id[] = $value->id;
            }          
            $query->whereIn('tenant_id', $tenant_id);
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
            BelongsTo::make('Арендатор', 'tenant', 'App\Nova\Tenant')->rules('required'),
            BelongsTo::make('Транспорт', 'transport', 'App\Nova\Transport')->rules('required'),
            Currency::make('Движение', 'price')->rules('required','numeric'),
            Text::make('Описание', 'comment')->rules('required'),
            Image::make('Фото', 'photo'),
            DateTime::make('Создано', 'created_at')->default(now())->rules('required')->readonly(true),
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
        return [
        ];
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
