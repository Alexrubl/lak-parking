<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;
use CArbon\Carbon;

class Log extends Resource
{
    public static $group = ' Отчеты';
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Log>
     */
    public static $model = \App\Models\Log::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'text', 'created_at'
    ];

    public static function label() {
        return 'Логи';
    }

    public static function singularlabel() {
        return 'Лог';
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
            ID::make()->sortable()->hideFromIndex(),
            Text::make('Контроллер', 'controller_id')->readonly(true)->nullable()->hideFromIndex(),
            Text::make('Направление', 'entry')->readonly(true)->nullable()->hideFromIndex(),
            Text::make('Событие', 'text')->readonly(true)->nullable(),
            Image::make('Фото', 'image')->showOnDetail(function (NovaRequest $request, $resource) {
                return $this->image;
            })->readonly(true)->nullable(),
            DateTime::make('Создано', 'created_at')->default(Carbon::now())->rules('required')->readonly(true),
        ];
    }

    /**
     * Get the fields displayed by the resource on detail page.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForDetail(NovaRequest $request)
    {
        return [
            ID::make()->sortable()->hideFromIndex(),
            Text::make('Контроллер', 'controller_id')->readonly(true)->nullable()->hideFromIndex(),
            Text::make('Направление', 'entry')->readonly(true)->nullable()->hideFromIndex(),
            Text::make('Событие', 'text')->readonly(true)->nullable(),
            Image::make('Фото', 'image')->maxWidth(300)->readonly(true)->nullable(),
            DateTime::make('Создано', 'created_at')->default(Carbon::now())->rules('required')->readonly(true),
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
