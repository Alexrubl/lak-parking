<?php

namespace Alexrubl\TimeRange;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class TimeRange extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'time-range';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        if (is_array($name)) $name = implode('-', $name);
        if (is_array($attribute)) $attribute = implode('-', $attribute);

        parent::__construct($name, $attribute, $resolveCallback);
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            [$from, $to] = $this->parseAttribute($attribute);
            [$valueFrom, $valueTo] = $this->parseResponse($request[$requestAttribute]);
            data_set($model, $to, $valueTo);
            data_set($model, $from, $valueFrom);
        }
    }

    protected function resolveAttribute($resource, $attribute)
    {
        [$from, $to] = $this->parseAttribute($attribute);
        $fromValue = data_get($resource, $from);
        $toValue = data_get($resource, $to);
        return ($fromValue ? $fromValue : null) .' - '. ($toValue? $toValue: null) ;
    }

    protected function parseAttribute($attribute)
    {   
        return explode('-', $attribute);
    }

    protected function parseResponse($attribute)
    {
        if ($attribute === null) {
            return [null, null];
        }
        return explode(',', $attribute);
    }


}
