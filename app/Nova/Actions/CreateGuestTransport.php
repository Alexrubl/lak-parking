<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\FormData;
use App\Models\Transport;
use App\Models\Rate;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreateGuestTransport extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Создать разовый пропуск';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {

        $model = Transport::withTrashed()->updateOrCreate(
            [
                'number' => $fields->number
            ],
            [
                'name' => 'Гостевой пропуск ' . $fields->number,
                'driver' => 'Гость',
                'type_id' => $fields->type->id,
                'tenant_id' => isset($fields->tenant->id) ? $fields->tenant->id : \Auth::user()->tenant()->first()->id,
                'rate_id' => Rate::where('default_guest', 1)->first()->id,
                'guest' => 1,
                'access' => 1,
                'deleted_at' => null
            ]
        );

        return Action::message('Готово!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [ 
            Text::make('Номер ТС', 'number')
                ->sortable()
                ->rules('required', function($attribute, $value, $fail) {
                    if (!preg_match("/^([a-zA-Z])\s?(\d)\s?(\d{2})\s?([a-zA-Z]{2})\s?(\d{2,3})$/ui",$value)) {
                        return $fail('Не правильный формат номера.');
                    }
                    return true;
                })
                ->help('на английской раскладке'),
            
            BelongsTo::make('Тип ТС', 'type', 'App\Nova\TypeTransport')->rules('required'),            

            $request->user()->tenant->count() != 1 ? BelongsTo::make('Арендатор', 'tenant', 'App\Nova\Tenant')->rules('required')->default(($request->user()->tenant->count() == 1 && $request->user()->isTenant()) ? $request->user()->tenant[0]->id : null)
                ->withoutTrashed()->searchable(!$request->user()->isTenant()) : Hidden::make('Require Verification'),

        ];
    }
}
