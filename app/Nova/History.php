<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ganyicz\NovaCallbacks\HasCallbacks;
use Titasgailius\SearchRelations\SearchesRelations;
use Alexrubl\Daterangefilter\Enums\Config;
use Storage;
use App\Nova\Metrics\HistorySumPerDay;
use App\Nova\Metrics\HistorySum;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class History extends Resource
{
    use SearchesRelations;
    
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

    public static $perPageOptions = [50, 100, 150, 1000];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'tenant_id', 'transport_id','comment', 'created_at'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'transport' => ['name', 'number'],
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isSecurity()) {
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
            BelongsTo::make('Транспорт', 'transport', 'App\Nova\Transport')
                ->displayUsing(function ($state) {
                    return $state->name .' ('.$state->number.')';
                })->searchable()
                ->rules('required'),
            Currency::make('Движение', 'price')->rules('required','numeric'),
            Text::make('Описание', 'comment')->rules('required'),
            Image::make('Фото', 'image')->showOnDetail(function (NovaRequest $request, $resource) {
                return $this->image;
            })->readonly(true)->nullable(),  
            DateTime::make('Создано', 'created_at')->default(now())->rules('required')->readonly(true),
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
            ID::make()->sortable(),
            BelongsTo::make('Арендатор', 'tenant', 'App\Nova\Tenant')->rules('required'),
            BelongsTo::make('Транспорт', 'transport', 'App\Nova\Transport')
                ->displayUsing(function ($state) {
                    return $state->name .' ('.$state->number.')';
                })->searchable()
                ->rules('required'),
            Currency::make('Движение', 'price')->rules('required','numeric'),
            Text::make('Описание', 'comment')->rules('required'),
            Image::make('Фото', 'image')->maxWidth(300)->readonly(true)->nullable(),
                //->thumbnail(function ($value) {
                //    return "image";
               // }),   
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
            HistorySumPerDay::make()->refreshWhenFiltersChange()->width('1/4')->defaultRange('30'),
            HistorySum::make()->refreshWhenFiltersChange()->width('1/4')->defaultRange('MTD'),
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
        if ($request->user()->isAdmin()) {
            return [
                new \App\Nova\Filters\TenantFilter,
                new \App\Nova\Filters\EntryOnly,
                new \App\Nova\Filters\HistoryBoolean,
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
        } else {
            return [
                new \App\Nova\Filters\EntryOnly,
                new \App\Nova\Filters\HistoryBoolean,
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
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [
            
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            // ExportAsCsv::make()
            // ->icon('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            //             <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            //         </svg>'),
            (new DownloadExcel)->askForFilename()->askForWriterType()->withHeadings()
            ->icon('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>', label: ''),
        ];
    }

 

}
