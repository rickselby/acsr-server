<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Services\PointsSequenceService;
use Illuminate\Support\Collection;

class StandingsService
{
    /** @var PointsSequenceService */
    protected $pointsSequenceService;

    public function __construct(PointsSequenceService $pointsSequenceService)
    {
        $this->pointsSequenceService = $pointsSequenceService;
    }

    /**
     * Get the standings from the heats for the given event
     *
     * @param Event $event
     *
     * @return mixed
     */
    public function heatStandings(Event $event)
    {
        $entrants = [];

        $pointsSequence = $this->pointsSequenceService->get($event->pointsSequence);

        foreach($event->races->where('heat', true)->where('complete', true) AS $race) {
            foreach($race->entrants AS $entrant) {
                if (!isset($entrants[$entrant->user->id])) {
                    $entrants[$entrant->user->id] = [
                        'user' => $entrant->user,
                        'points' => 0,
                        'positions' => new Collection(),
                        'fastestLap' => PHP_INT_MAX,
                    ];
                }

                $entrants[$entrant->user->id]['positions']->push($entrant->position);
                $entrants[$entrant->user->id]['points'] += $pointsSequence[$entrant->position] ?? 0;
                $entrants[$entrant->user->id]['fastestLap'] = min($entrants[$entrant->user->id]['fastestLap'], $entrant->fastest_lap);
            }
        }

        usort($entrants, [$this, 'sortStandings']);

        return $this->addPositions($entrants);
    }

    /**
     * Function for usort to sort standings
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    protected function sortStandings($a, $b)
    {
        /**
         * First, sort by points, if they're different
         */
        if ($a['points'] != $b['points']) {
            return $b['points'] - $a['points'];
        }

        /**
         * Then, best finishing positions; all the way down...
         */

        // First, get the positions collections sorted
        $positions = [
            'a' => $a['positions']->sort(),
            'b' => $b['positions']->sort(),
        ];

        for($i = 0; $i < max($positions['a']->count(), $positions['b']->count()); $i++) {

            // Get the next best position for each driver (null if empty)
            $val['a'] = $positions['a']->shift();
            $val['b'] = $positions['b']->shift();

            // Check both positions are not null
            if ($val['a'] && $val['b']) {
                // If they're different, compare them
                // If not, loop again
                if ($val['a'] != $val['b']) {
                    return $val['a'] - $val['b'];
                }
            } elseif ($val['a']) {
                // $a has more results; $b takes priority (same points, less races)
                return -1;
            } elseif ($val['b']) {
                // $b has more results; $a takes priority (same points, less races)
                return 1;
            }
        }

        /**
         * If we're here, points and finishing positions are identical
         * So we compare their fastest laps
         */
        return $a['fastestLap'] - $b['fastestLap'];
    }

    /**
     * Add positions to an array
     *
     * @param [] $array
     *
     * @return []
     */
    protected function addPositions($array)
    {
        $position = 1;
        $arrayKeys = array_keys($array);
        foreach($arrayKeys AS $index => $key) {
            $array[$key]['position'] = $position++;
            // See if the previous result is the same as this result
            if ($index > 0 && $this->areEqual($array[$key], $array[$arrayKeys[$index-1]])) {
                // If it is, copy the position from the previous result
                $array[$key]['position'] = $array[$arrayKeys[$index-1]]['position'];
            }
        }
        return $array;
    }

    /**
     * Check if two standings are equal
     *
     * @param $a
     * @param $b
     *
     * @return bool
     */
    protected function areEqual($a, $b)
    {
        return ($a['points'] == $b['points'])
            && ($a['positions'] == $b['positions'])
            && ($a['fastestLap'] == $b['fastestLap']);
    }


}