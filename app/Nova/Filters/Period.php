<?php

namespace App\Nova\Filters;

use Alexrubl\Daterangefilter\Daterangefilter;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;

class Period extends Daterangefilter
{
    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name = 'Период';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        $from = Carbon::parse($value[0])->startOfDay();
        $to = Carbon::parse($value[1])->endOfDay();
        info($value);

        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function options(NovaRequest $request)
    // {
    //     return [
    //         'firstDayOfWeek' => 0,
    //         'separator' => '-',
    //         'mode' => 'range',
    //         'enableTime' => false,
    //         'enableSeconds' => false,
    //         'twelveHourTime' => false
    //     ];
    // }
}
