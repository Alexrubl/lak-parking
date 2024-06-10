<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use App\Models\History;

class HistorySum extends Value
{
    /**
     * Get the displayable name of the metric
     *
     * @return string
     */
    public function name()
    {
        return 'За период';
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->sum($request, History::class, 'price')->suffix('руб.');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            7 => Nova::__('7 Days'),
            30 => Nova::__('30 Days'),
            60 => Nova::__('60 Days'),
            90 => Nova::__('90 Days'),
            'TODAY' => Nova::__('Today'),
            'YESTERDAY' => Nova::__('Yesterday'),
            'MTD' => Nova::__('Month To Date'),
            'QTD' => Nova::__('Quarter To Date'),
            'YTD' => Nova::__('Year To Date'),
            'ALL' => Nova::__('All Time')
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'history-summ';
    }
}
