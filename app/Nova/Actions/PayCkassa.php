<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Text;
// use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Http\Controllers\CkassaController as Ckassa;
use Illuminate\Support\Facades\Auth;

class PayCkassa extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Оплата через CKassa';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        //info($fields);
        $resp = json_decode(Ckassa::invoice($models, $fields, Auth::user()));
        //info('Ответ: ');
        // info($resp->payUrl);
        // info($resp);
        // return Action::openInNewTab('https://example.com');
        //return Action::redirect('https://example.com');    
        if (isset($resp->payUrl)) {    
            return Action::openInNewTab($resp->payUrl);
        }
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
            Text::make('Сумма', 'sum')->rules('required')->help('Минимальный платёж 50 р.')
        ];
    }
}
