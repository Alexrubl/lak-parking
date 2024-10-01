<?php

namespace App\Nova\Actions;

use App\Http\Controllers\CkassaController as Ckassa;
// use Laravel\Nova\Actions\DestructiveAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $resp = json_decode(Ckassa::invoice($models, $fields, Auth::user()));
        if (isset($resp->payUrl)) {
            return Action::openInNewTab($resp->payUrl);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Currency::make('Сумма', 'sum')->rules('required')->min(50)->help('Минимальный платёж 50 р.'),
        ];
    }
}
