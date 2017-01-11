<?php

namespace App\Services\Grids\LeftRightGrids;

class AssignRaces
{
    protected $matches;

    /**
     * Pseudo-random; join the race with the drivers we've raced against the least.
     *
     * @param $sessions
     *
     * @return array
     */
    public function assign($sessions)
    {
        $this->matches = [];
        $races = [];

        // Shuffle the sessions, maintaining keys
        uksort($sessions, function() { return rand() > getrandmax() / 2; });

        foreach($sessions AS $sessionID => $session) {
            // Generate the correct number of new races for this session
            $newRaces = [];
            for ($i = 1; $i <= $session['heats']; $i++) {
                $newRaces[$i] = [
                    'slots' => [],
                    'session' => $sessionID,
                ];
            }

            // Shuffle the slots, maintaining keys
            uksort($session['slots'], function() { return rand() > getrandmax() / 2; });

            // Step through each session
            // (could this be done in a random order too?)
            foreach($session['slots'] AS $slotID => $drivers) {
                foreach ($newRaces AS $id => $race) {
                    $newRaces[$id]['slots'][$slotID] = [];
                }

                // Assign all drivers from the session to races
                while (count($sessions[$sessionID]['slots'][$slotID])) {
                    // Pick a random driver from the slot
                    $id = array_rand($sessions[$sessionID]['slots'][$slotID]);
                    $driver = $sessions[$sessionID]['slots'][$slotID][$id];

                    // See what races have space for this driver
                    $available = $this->getAvailableRaces($newRaces, $slotID);
                    if (count($available) == 1) {
                        // if there is only one race with spaces, then it's that one
                        $key = array_keys($available)[0];
                    } else {
                        // otherwise, work out the 'value' of each race and assign accordingly
                        $key = $this->getRaceFor($driver, $available);
                    }
                    // Assign the matches for the driver joining this race
                    $this->assignMatchesFor($driver, $newRaces[$key]);
                    // Actually add the driver to the race
                    $newRaces[$key]['slots'][$slotID][] = $sessions[$sessionID]['slots'][$slotID][$id];
                    // Remove the driver from the session
                    unset($sessions[$sessionID]['slots'][$slotID][$id]);
                }
            }

            // Move the new races to be proper races
            foreach($newRaces AS $race) {
                $races[] = $race;
            }
        }

        return $races;
    }

    /**
     * Get a list of races that have space, with a list of current drivers
     *
     * @param $raceList
     * @param $slot
     *
     * @return array
     */
    protected function getAvailableRaces($raceList, $slot)
    {
        $available = [];
        foreach($raceList AS $raceID => $race) {
            if (count($race['slots'][$slot]) != 2) {
                // Get the list of drivers already in this race
                $available[$raceID] = [];
                array_walk_recursive($race['slots'], function($a) use (&$available, $raceID) {
                    $available[$raceID][] = $a;
                });
            }
        }
        return $available;
    }

    /**
     * Select a race for the given driver from the races, based on who they have raced against
     * the least
     *
     * @param $driverID
     * @param $races
     *
     * @return mixed
     */
    protected function getRaceFor($driverID, $races)
    {
        $values = [];
        foreach($races AS $id => $drivers) {
            $values[$id] = 0;
            foreach($drivers AS $driver) {
                $values[$id] += $this->getValue($driver, $driverID);
            }
        }
        $min = min($values);
        $mins = array_filter($values, function($v) use ($min) {
            return $v == $min;
        });

        return array_rand($mins);
    }

    /**
     * Assign the matches for the given driver joining the given race
     *
     * @param $driverID
     * @param $race
     */
    protected function assignMatchesFor($driverID, $race)
    {
        foreach($race['slots'] AS $slot) {
            foreach($slot AS $driver) {
                $this->racedTogether($driver, $driverID);
            }
        }
    }

    /**
     * Mark two drivers as having raced together
     *
     * @param $a
     * @param $b
     */
    protected function racedTogether($a, $b)
    {
        $x = min($a, $b);
        $y = max($a, $b);

        if (!isset($this->matches[$x])) {
            $this->matches[$x] = [];
        }
        if (!isset($this->matches[$x][$y])) {
            $this->matches[$x][$y] = 0;
        }
        $this->matches[$x][$y]++;
    }

    /**
     * Get the number of times two drivers have raced together
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    protected function getValue($a, $b)
    {
        $x = min($a, $b);
        $y = max($a, $b);

        if (!isset($this->matches[$x])) {
            return 0;
        }
        if (!isset($this->matches[$x][$y])) {
            return 0;
        }
        return $this->matches[$x][$y];
    }

}
