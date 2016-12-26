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

    /**
     * Convert a race time in milliseconds to a readable time
     *
     * @param int $value
     *
     * @return string
     */
    public function toString($value)
    {
        if ($value && is_numeric($value)) {
            $milliseconds = $value % 1000;
            $seconds = (($value - $milliseconds) / 1000) % 60;
            $minutes = ($value - ($seconds * 1000) - $milliseconds) / (1000 * 60);
            if ($minutes > 59) {
                $minutes = $minutes % 60;
                $hours = ($value - ($minutes * (1000 * 60)) - ($seconds * 1000) - $milliseconds) / (1000 * 60 * 60);
                $hoursMinutesString = $hours.':'.str_pad($minutes, 2, '0', STR_PAD_LEFT);
            } else {
                $hoursMinutesString = $minutes;
            }

            return $hoursMinutesString.':'
                .str_pad($seconds, 2, '0', STR_PAD_LEFT).'.'
                .str_pad($milliseconds, 3, '0', STR_PAD_LEFT);
        } else {
            if (is_string($value)) {
                return $value;
            } else {
                return '';
            }
        }
    }

}
