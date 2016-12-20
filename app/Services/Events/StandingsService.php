<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Services\PointsSequenceService;

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
                        'positions' => [],
                        'fastestLap' => PHP_INT_MAX,
                    ];
                }

                $entrants[$entrant->user->id]['positions'][] = $entrant->position;
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
        // First, sort by points, if they're different
        if ($a['points'] != $b['points']) {
            return $b['points'] - $a['points'];
        }

        // Then, best finishing positions; all the way down...
        $positions = [
            'a' => array_values($a['positions']),
            'b' => array_values($b['positions']),
        ];
        sort($positions['a']);
        sort($positions['b']);

        for($i = 0; $i < max(count($positions['a']), count($positions['b'])); $i++) {
            // Check both have a position set
            if (isset($positions['a'][$i]) && isset($positions['b'][$i])) {
                // If they're different, compare them
                // If not, loop again
                if ($positions['a'][$i] != $positions['b'][$i]) {
                    return $positions['a'][$i] - $positions['b'][$i];
                }
            } elseif (isset($positions['a'][$i])) {
                // $a has less results; $b takes priority
                return -1;
            } elseif (isset($positions['b'][$i])) {
                // $b has less results; $a takes priority
                return 1;
            }
        }

        // Otherwise, it's the fastest lap; this could return zero if drivers cannot be split
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