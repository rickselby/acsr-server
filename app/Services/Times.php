<?php

namespace App\Services;

use Carbon\Carbon;

class Times
{
    /**
     * Show a time in the current users' timezone
     *
     * @param Carbon $time
     * @param string $format
     *
     * @return string
     */
    public function toUserTimezone(Carbon $time, $format = 'l jS F Y, H:i')
    {
        if (\Auth::check() && \Auth::user()->timezone) {
            return \Timezone::convertFromUTC(
                $time,
                \Auth::user()->timezone,
                $format
            );
        } else {
            return $time->format($format.' e');
        }
    }

    /**
     * Convert an entered time from the users' timezone for storing
     *
     * @param Carbon $time
     *
     * @return string
     */
    public function fromUserTimezone(Carbon $time)
    {
        if (\Auth::check() && \Auth::user()->timezone) {
            return \Timezone::convertToUTC(
                $time,
                \Auth::user()->timezone
            );
        } else {
            return $time->format('Y-m-d H:i:s');
        }
    }
}
