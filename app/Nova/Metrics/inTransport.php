<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Models\Transport;
use Laravel\Nova\Nova;
use DB;

class inTransport extends Value
{
    /**
     * Get the displayable name of the metric
     *
     * @return string
     */
    public function name()
    {
        return 'Находятся на территории';
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $timezone = Nova::resolveUserTimezone($request) ?? $request->timezone;      
        //return $this->result(DB::table('transports')->whereBetween( 'created_at', $this->currentRange($request->range, $timezone))->count());
        return $this->count($request, Transport::inside(), null, 'updated_at')->suffix('шт.');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        
        return [
            'TODAY' => Nova::__('Today'),
            'YESTERDAY' => Nova::__('Yesterday'),
            30 => Nova::__('30 Days'),                       
            60 => Nova::__('60 Days'),
            365 => Nova::__('365 Days'),            
            'MTD' => Nova::__('Month To Date'),
            'QTD' => Nova::__('Quarter To Date'),
            'YTD' => Nova::__('Year To Date'),
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
        return 'in-transport';
    }
}
