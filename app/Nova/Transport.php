<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasOne;
use Ganyicz\NovaCallbacks\HasCallbacks;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Controller;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Laravel\Nova\Panel;
use Laravel\Nova\Nova;
use App\Nova\Metrics\inTransport;
use Laravel\Nova\Actions\Action;
use Formfeed\DependablePanel\DependablePanel;
use Alexrubl\DateRange\DateRange;
use Alexrubl\TimeRange\TimeRange;
use App\Http\Controllers\ApiController as Api;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;


class Transport extends Resource    
{
    use HasCallbacks;

    public static $group = '  Справочники';

    public static $priority = 2;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Transport>
     */
    public static $model = \App\Models\Transport::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label() {
        return 'Транспорты';
    }

    public static function singularlabel() {
        return 'Транспорт';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'driver', 'number'
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

            Text::make('Наименование', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Водитель', 'driver')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Номер ТС', 'number')
                ->sortable()
                ->rules('required', 'max:10')
                ->help('на английской раскладке')
                ->creationRules('unique:transports,number')
                ->updateRules('alpha_num:ascii','unique:transports,number,{{resourceId}}'),
            
            BelongsTo::make('Тип ТС', 'type', 'App\Nova\TypeTransport')->showCreateRelationButton()->rules('required'),            

            BelongsTo::make('Арендатор', 'tenant', 'App\Nova\Tenant')->rules('required')
                // ->withMeta([
                //     'belongsToId' => ($request->user()->tenant->count() == 1 && $request->user()->isTenant()) ? $request->user()->tenant[0]->id : null
                // ])]
                ->default(($request->user()->tenant->count() == 1 && $request->user()->isTenant()) ? $request->user()->tenant[0]->id : null)
                ->withoutTrashed()->searchable(!$request->user()->isTenant()),
                // ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                //     info($request);
                //     info($model);
                //     info($attribute);
                //     info($requestAttribute);
                //     if ($request->user()->tenant->count() == 1 && $request->user()->isTenant()) {
                //         $model->{$attribute} = $request->user()->tenant[0]->id;
                //     } else {
                //         $model->{$attribute} = $request->tenant;
                //     }
                // })
                
                // ->canSee(function ($request) {
                //     if ($request->user()->tenant->count() == 1) {
                //         return !$request->user()->isTenant();
                //     }
                //     return true;
                // }),

            BelongsTo::make('Тариф', 'rate', 'App\Nova\Rate')->rules('required')
                ->dependsOn('guest', function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                    if ($formData->guest === true) {
                        $field->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                            $query->where('default_guest', true);
                        });
                    }
                }),   

            Boolean::make('На территории', 'inside')->onlyOnIndex(),
            
            Boolean::make('Доступ', 'access'),
            
            Boolean::make('Гостевой', 'guest'),

            Boolean::make('Ограничения', 'restrictions')->hideFromIndex(),
            
            DependablePanel::make('Расписание', $this->scheduleFields())
                ->dependsOn(
                    ['guest', 'restrictions'],
                    function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                        if ($formData->guest == true || $formData->restrictions == false) {
                            $panel->hide();
                        }
                    }
                )->separatePanel(true),
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
            // \Alexrubl\Toolbar\Toolbar::make(),
            inTransport::make()->refreshWhenFiltersChange()->width('1/4')->icon('question-mark-circle'),
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
        return [
            (new \App\Nova\Filters\TenantFilter)->canSee(fn ($request) => $request->user()->isAdmin()),
        ];
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
        return [
            //(new \App\Nova\Actions\SaveAllTransport)->standalone()
            \App\Nova\Actions\CreateGuestTransport::make()
            ->standalone()->onlyOnIndex()
            ->icon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>'),
            Action::using('Закрыть доступ', function (ActionFields $fields, Collection $models) {
                $models->each->update(['access' => false]);
            })->icon('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>'),            
        ];
    }

    protected function scheduleFields()
    {
        $model = ['From', 'To'];
        return [
            // Boolean::make('Гостевой', 'guest'),
            
            AdvancedNumber::make('Ограничение по таймауту', 'time_limit')->default(0)->step(1)->hideFromIndex()->help('Задаётся в часах'),
            TimeRange::make('Временной интервал', ['fromTime', 'toTime'])->hideFromIndex(),
            DateRange::make('Период', ['fromDate', 'toDate'])->hideFromIndex(),
            BooleanGroup::make('Дни недели', 'week')->options([
                'Понедельник' => 'Понедельник',
                'Вторник' => 'Вторник',
                'Среда' => 'Среда',
                'Четверг' => 'Четверг',
                'Пятница' => 'Пятница',
                'Суббота' => 'Суббота',
                'Воскресенье' => 'Воскресенье',
            ])->default([
                'Понедельник' => true,
                'Вторник' => true,
                'Среда' => true,
                'Четверг' => true,
                'Пятница' => true,
                'Суббота' => true,
                'Воскресенье' => true,
            ])->hideFromIndex(),
        ];
    }

    public static function afterCreate(Request $request, $model) {  
          
    }

    public static function afterSave(Request $request, $model) {
  
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }
}
