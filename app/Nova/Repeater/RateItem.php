<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class RateItem extends Repeatable
{
    public static $title = 'name';

    public static function label()
    {
        return 'Тарифы';
    }

    public static function singularlabel()
    {
        return 'Тариф';
    }

    /**
     * Get the fields displayed by the repeatable.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Тип ТС', 'type_transport')->options(function () {
                foreach (\App\Models\TypeTransport::all() as $key => $value) {
                    $arr[$value->id] = $value->name;
                }

                return $arr;
            })->rules('required')->displayUsingLabels(),
            Currency::make('Стоимость', 'price')->currency('Rub')->rules('required', 'numeric')->help('за въезд')->rules('required'),
        ];
    }
}
