<?php

namespace App\Nova\Metrics;

use App\Driver;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class DriverPartition extends Partition
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return __('Drivers Availability');
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Driver::class, 'busy')
            ->label(function ($value) {
                switch ($value) {
                    case 0:
                        return 'Free';
                    case 1:
                        return 'Busy';
                    default:
                        return ucfirst($value);
                }
            })->colors([
                0 => '#27ae60',
                1 => '#c0392b',
                // photo will use the default color from Nova
            ]);
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
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
        return 'driver-partition';
    }
}
