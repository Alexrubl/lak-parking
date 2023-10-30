<?php

namespace Alexrubl\DateRange;

use DateTimeInterface;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Carbon\Carbon;

class DateRange extends Field
{
    const DEFAULT_SEPERATOR = '-';
    const DEFAULT_MODE = 'range';
    protected $seperator;
    protected $mode;
    protected $format = 'd.m.Y';

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-range';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        if (is_array($name)) $name = implode('-', $name);
        if (is_array($attribute)) $attribute = implode('-', $attribute);

        $this->seperator(static::DEFAULT_SEPERATOR);
        $this->mode(static::DEFAULT_MODE);

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

        return $fromValue ? (Carbon::parse($fromValue)->format($this->format)." $this->seperator ".($toValue ? Carbon::parse($toValue)->format($this->format) : '/')) : null;
    }

    /**
     * Set the date format (Moment.js) that should be used to display the date.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;
        return $this->withMeta(['format' => $format]);
    }

    	/**
	 * Indicate that the field should be nullable.
	 *
	 * @param  bool $nullable
	 * @param  array|Closure $values
	 * @return $this
	 */
    public function nullable($nullable = true, $values = null)
    {
        return $this->withMeta(['nullable' => $nullable]);
    }

    /**
     * Set the seperator for the field's dates
     *
     * @param $seperator
     * @return $this
     */
    public function seperator($seperator)
    {
        $this->seperator = $seperator;
        return $this->withMeta(['seperator' => $seperator]);
    }

    public function enableTime($enableTime)
    {
        return $this->withMeta(['enableTime' => $enableTime]);
    }

    public function noCalendar($noCalendar)
    {
        return $this->withMeta(['noCalendar' => $noCalendar]);
    }

    public function mode($mode)
    {
        $this->mode = $mode;
        return $this->withMeta(['mode' => $mode]);
    }

    /**
     * Set the first day of the week.
     *
     * @param  int  $day
     * @return $this
     */
    public function firstDayOfWeek($day)
    {
        return $this->withMeta([__FUNCTION__ => $day]);
    }

    /**
     * Parse the attribute name to retrieve the affected model attributes
     *
     * @param $attribute
     * @return array
     */
    protected function parseAttribute($attribute)
    {   
        return explode('-', $attribute);
    }

    /**
     * Parse the response to retrieve the raw values
     *
     * @param $attribute
     * @return array
     */
    protected function parseResponse($attribute)
    {
        if ($attribute === null) {
            return [null, null];
        }
        $attribute = explode(" $this->seperator ", $attribute);
        return array_pad([Carbon::parse($attribute[0]), Carbon::parse($attribute[1])], 2, null);
    }

}
