<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use App\Models\Transport;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Http\Controllers\ApiController as Api;

class SaveAllTransport extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Обновить транспорты на всех контроллерах';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach (Transport::all() as $key => $transport) {
            try {
                $api = new Api;
                $api->sendNewTransportToControllers($transport);            
            } catch (\Throwable $th) {
                info($th->getMessage());
                continue;
            } 
        }
        return Action::message('Обновление транспорта на контроллерах закончено!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
