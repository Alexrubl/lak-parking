<?php

namespace App\Nova\Actions;

use App\Http\Controllers\CkassaController as Ckassa;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class PayCkassaStatus extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        info($fields);
        $resp = Ckassa::status();
        info($resp);

        // return Action::openInNewTab('https://example.com');
        //return Action::redirect('https://example.com');
        return Action::message($resp);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
