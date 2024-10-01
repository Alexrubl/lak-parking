<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class BelongsToForActions extends BelongsTo
{
    public function fillForAction(NovaRequest $request, $model)
    {
        info($request);
        $attribute = $this->attribute;

        if ($request->exists($attribute)) {
            $value = $request[$attribute];

            $model->{$attribute} = $this->isNullValue($value) ? null : $value;
        }
    }
}
