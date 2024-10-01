<?php

namespace App\Nova;

use Alexrubl\Daterangefilter\Enums\Config;
use CArbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

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
    public static $title = 'text';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'text', 'created_at','entry'
    ];

    public static function label()
    {
        return 'Логи';
    }

    public static function singularlabel()
    {
        return 'Лог';
    }

    /**
     * Get the fields displayed by the resource.
     *
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
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            new \App\Nova\Filters\Period('Created at', 'created_at', [
                Config::ALLOW_INPUT => false,
                Config::DATE_FORMAT => 'd-m-Y',
                Config::DISABLED => false,
                Config::ENABLE_TIME => false,
                Config::ENABLE_SECONDS => false,
                Config::FIRST_DAY_OF_WEEK => 0,
                Config::LOCALE => 'ru',
                Config::PLACEHOLDER => __('Выберите период'),
                Config::SHORTHAND_CURRENT_MONTH => false,
                Config::SHOW_MONTHS => 1,
                Config::TIME24HR => true,
                Config::WEEK_NUMBERS => false,
            ]),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new DownloadExcel)->askForFilename()->askForWriterType()->withHeadings()
                ->icon('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>', label: 'Выгрузить'),
        ];
    }
}
