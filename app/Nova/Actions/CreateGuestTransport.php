<?php

namespace App\Nova\Actions;

use Alexrubl\MaskInput\MaskInput;
use App\Models\History;
use App\Models\Rate;
use App\Models\Tenant;
use App\Models\Transport;
use App\Nova\Fields\BelongsToForActions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreateGuestTransport extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Создать разовый пропуск';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $model = Transport::withTrashed()->where([
            ['rate_id', '<>', Rate::where('default_guest', 1)->first()->id],
            ['number' , str_replace([' '], '', $fields->number)]
        ])->first();
        if (isset($model)) {
            return Action::danger('Транспорт уже существует на постоянной основе!');
        }
        $model = Transport::withTrashed()->updateOrCreate(
            [
                'number' => str_replace([' '], '', $fields->number),
            ],
            [
                'name' => 'Гостевой разовый пропуск '.$fields->number,
                'driver' => 'Гость',
                'type_id' => $fields->type,
                'tenant_id' => isset($fields->tenant) ? $fields->tenant : \Auth::user()->tenant->first()->id,
                'rate_id' => Rate::where('default_guest', 1)->first()->id,
                'guest' => 1,
                'access' => 1,
                'deleted_at' => null,
            ]
        );

        $history = new History;
        $history->tenant_id = isset($fields->tenant) ? $fields->tenant : \Auth::user()->tenant->first()->id;
        $history->transport_id = $model->id;
        $history->comment = 'Создание разового пропуска '.$model->number.' - '.$model->tenant->name;
        $history->save();

        logist('Создание разового пропуска. Транспорт: '.$model->number.', Арендатор: '.$model->tenant->name.'. Создан: '.\Auth::user()->name.' ('.\Auth::user()->id.')');

        return Action::message('Создан разовый пропуск');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            MaskInput::make('Номер ТС', 'number')
                ->sortable()
                ->mask('Z ### ZZ ###')
                ->rules(['required','unique:transports','max:12'])
                ->help('на английской раскладке'),

            // BelongsToForActions::make('Тип ТС', 'type', 'App\Nova\TypeTransport')->rules('required'),
            Select::make('Тип ТС', 'type')->options(\App\Models\TypeTransport::pluck('Name', 'id'))->rules('required'),

            // $request->user()->tenant->count() != 1 ? BelongsToForActions::make('Арендатор', 'tenant', 'App\Nova\Tenant')->default(($request->user()->tenant->count() == 1 && $request->user()->isTenant()) ? $request->user()->tenant[0]->id : null)
            //     ->withoutTrashed()->searchable(!$request->user()->isTenant()) : Hidden::make('Require Verification')->rules('required'),

            $request->user()->tenant->count() != 1
                ? Select::make('Арендатор', 'tenant')->options($request->user()->isTenant() ? $request->user()->tenant->pluck('name', 'id') : Tenant::all()->pluck('name', 'id'))->rules('required')->default(($request->user()->tenant->count() == 1 && $request->user()->isTenant()) ? $request->user()->tenant[0]->id : null)->searchable(! $request->user()->isTenant())
                : Hidden::make('Require Verification'),
        ];
    }

    public function uriKey()
    {
        return 'create-guest-transport';
    }
}
