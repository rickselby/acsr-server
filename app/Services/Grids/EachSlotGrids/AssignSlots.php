<?php

namespace App\Services\Grids\EachSlotGrids;

use App\Exceptions\Grids\AssignSlotFailed;

class AssignSlots
{
    /**
     * Array 'framework' of the sessions and their slots
     * @var array
     */
    protected $sessions;

    /**
     * Array of drivers and their selected options
     * @var array
     */
    protected $drivers = [];

    /**
     * Count of number of sessions each driver should sit out
     * @var int
     */
    protected $sitOuts;

    /**
     * Assign drivers to slots for each session
     *
     * @param int $driverCount      Number of drivers in event
     * @param int $servers          Number of parallel heats
     *
     * @return array
     */
    public function assign($driverCount, $servers)
    {
        // Generate the array framework to work with
        $this->generateFramework($driverCount, $servers);

        /**
         * Step through each driver and assign them to sessions and slots
         */
        for ($driver = 1; $driver <= $driverCount; $driver++) {

            // Set up the driver details
            $this->drivers[$driver] = [
                'sessions' => [],
                'slots' => [],
                'sitouts' => 0,
            ];

            while(count($this->drivers[$driver]['sessions']) != count($this->sessions)) {

                // Get a list of options for the current driver
                $options = $this->getOptions($driver);

                if ($this->sessionHasOnlyOneOption($options)) {
                    // One session only has one option available, so assign to that one
                    $this->assignDriverToSessionWithOnlyOneOption($driver, $options);
                } elseif ($this->slotHasOnlyOneOption($options)) {
                    // One slot has only one option available, so assign to that one
                    $this->assignDriverToSlotWithOnlyOneOption($driver, $options);
                } elseif ($this->checkForMatchingOptions($options)) {
                    // There are options that are the same, and should be dealt with first...
                    $this->assignDriverToMatchingSession($driver, $options);
                } else {
                    // Nothing restricting us, so assign randomly
                    $this->assignDriverToRandomSession($driver, $options);
                }
            }
        }

        return $this->sessions;
    }

    /**
     * Generate an array 'framework' representing the sessions and slots avaliable
     *
     * @param int $driverCount
     * @param int $servers
     *
     * @return array
     */
    protected function generateFramework($driverCount, $servers)
    {
        // How many drivers per heat are there?
        $driversPerHeat = (int) floor($driverCount / $servers);
        // Each driver will race from each grid slot, so....
        $heatsPerDriver = $driversPerHeat;
        // And we are assigning directly to a grid slot, not to a row of the grid
        $heatSlots = $driversPerHeat;

        // How many heats do we need?
        $heatCount = $driverCount;

        // How many heats can we run at a time?
        $parallelHeats = $servers;

        // So, how many sessions...?
        $sessionCount = (int) ceil($heatCount / $parallelHeats);

        // Do we need people sitting out of sessions?
        $sitOuts = ($sessionCount - $heatsPerDriver);
        $this->sitOuts = $sitOuts;

        // Great! now... er?
        $this->sessions = [];

        // Generate a 'framework' of arrays for the heats
        for($sessionID = 1; $sessionID <= $sessionCount; $sessionID++) {
            $this->sessions[$sessionID] = [
                'heats' => min($parallelHeats, $heatCount - (($sessionID - 1) * $parallelHeats)),
                'slots' => [],
                'slotSize' => 0,
            ];

            $this->sessions[$sessionID]['slotSize'] = 2 * $this->sessions[$sessionID]['heats'];

            for ($slotID = 1; $slotID <= $heatSlots; $slotID++) {
                $this->sessions[$sessionID]['slots'][$slotID] = [];
            }

            if ($sitOuts != 0) {
                $this->sessions[$sessionID]['sitOuts'] = [];
                $this->sessions[$sessionID]['sitOutSize'] = ($driverCount - ($driversPerHeat * $this->sessions[$sessionID]['heats']));
            }
        }
    }

    /**
     * Get the session / slot options for the given driver
     *
     * @param $driver
     * @param $driverOptions
     * @param $sessions
     *
     * @return array
     */
    protected function getOptions($driver)
    {
        $options = [];
        foreach($this->sessions AS $sessionID => $session) {
            if (!in_array($sessionID, $this->drivers[$driver]['sessions'])) {
                $options[$sessionID] = $this->getOptionsFor($sessionID, $driver);
            }
        }

        return $options;
    }

    /**
     * Get the slot options for the given session and driver
     *
     * @param $sessionID
     * @param $driver
     *
     * @return array
     */
    protected function getOptionsFor($sessionID, $driver)
    {
        $options = [];
        // Check this driver hasn't already been assigned to this session
        if (!in_array($sessionID, $this->drivers[$driver]['sessions'])) {
            foreach ($this->sessions[$sessionID]['slots'] AS $slotID => $slot) {
                // Check if the driver has already been assigned to this slot in another session
                if (!in_array($slotID, $this->drivers[$driver]['slots'])
                    // Check if the slot is full
                    && count($slot) < $this->sessions[$sessionID]['slotSize']) {
                    $options[] = $slotID;
                }
            }

            // Check if the driver has already sat out the required number of sessions
            if ($this->drivers[$driver]['sitouts'] < $this->sitOuts) {
                // Check if the sitouts option is full
                if (count($this->sessions[$sessionID]['sitOuts']) < $this->sessions[$sessionID]['sitOutSize']) {
                    $options[] = 'SO';
                }
            }
        }

        return $options;
    }

    /**
     * Check if there is a session for which there is only one option available
     *
     * @param [] $options
     *
     * @return bool
     */
    protected function sessionHasOnlyOneOption($options)
    {
        foreach($options AS $option) {
            if (count($option) == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign the driver to the session that has only one option...
     *
     * @param $driver
     * @param $options
     *
     * @throws AssignSlotFailed
     */
    protected function assignDriverToSessionWithOnlyOneOption($driver, $options)
    {
        $sessionID = NULL;

        foreach($options AS $sessionID => $option) {
            if (count($option) == 1) {
                break;
            }
        }

        if ($sessionID) {
            $this->assignDriverToRandomSlot($driver, $sessionID);
        } else {
            throw new AssignSlotFailed('Could not find the session with only one option');
        }
    }

    /**
     * Check if any slot has only one option
     *
     * @param $options
     *
     * @return bool
     */
    protected function slotHasOnlyOneOption($options)
    {
        return in_array(1, $this->getSlotCounts($options));
    }

    /**
     * Get the number of options for each slot
     *
     * @param $options
     *
     * @return array
     */
    protected function getSlotCounts($options)
    {
        $slots = [];

        foreach ($options AS $option) {
            foreach($option AS $o) {
                if (!isset($slots[$o])) {
                    $slots[$o] = 0;
                }
                $slots[$o]++;
            }
        }
        return $slots;
    }

    /**
     * Assign the driver to the slot where there is only one option...
     *
     * @param $driver
     * @param $options
     *
     * @throws AssignSlotFailed
     */
    protected function assignDriverToSlotWithOnlyOneOption($driver, $options)
    {
        $slot = null;

        foreach($this->getSlotCounts($options) AS $slot => $count) {
            if ($count == 1) {
                break;
            }
        }

        if ($slot) {
            foreach ($options AS $sessionID => $option) {
                if (in_array($slot, $option)) {
                    $this->assignDriverToSessionSlot($driver, $sessionID, $slot);
                }
            }
        } else {
            throw new AssignSlotFailed('Could not find the slot with only one option');
        }
    }

    /**
     * Check if any options are identical, and should be prioritised
     *
     * @param $options
     *
     * @return int
     */
    protected function checkForMatchingOptions($options)
    {
        return count($this->getMatchingOptions($options));
    }

    /**
     * Get a list of matching options; options only match if the size of the array is the same as the number
     * that match (so 2 arrays of 3 options DON'T "match")
     *
     * @param $options
     *
     * @return array
     */
    protected function getMatchingOptions($options)
    {
        // Look for arrays of length 2, 3... up to the maximum
#        for ($i = 2; $i <= max(array_map("count", $options)); $i++) {
            $i = 2;

            // Get all the options from the array with that many values in them
            $opts = array_map("serialize", array_filter($options, function($var) use ($i) {
                return count($var) == $i;
            }));

            // If there are the same number of matches as the array length, and they're all the same,
            // return the keys
            if (count($opts) == $i && count(array_unique($opts)) == 1) {
                return array_keys($opts);
            }

            // If the count is less than the array length, we don't care.
            if (count($opts) > $i) {
                // Get a list of how many times each array of options appears
                $values = array_count_values($opts);
                foreach($values AS $str => $count) {
                    // If one of the arrays matches the array length, get the keys
                    // for where it appears, and return them
                    if ($count == $i) {
                        $keys = [];
                        foreach($opts AS $key => $val) {
                            if ($val == $str) {
                                $keys[] = $key;
                            }
                        }
                        return $keys;
                    }
                }
            }
#        }

        // No matches - empty array
        return [];
    }

    /**
     * Assign the driver to one of the matching sessions
     *
     * @param $driver
     * @param $options
     *
     * @throws AssignSlotFailed
     */
    protected function assignDriverToMatchingSession($driver, $options)
    {
        $dupes = $this->getMatchingOptions($options);
        if (count($dupes)) {
            $this->assignDriverToRandomSlot($driver, $dupes[array_rand($dupes)]);
        } else {
            throw new AssignSlotFailed('Matching Session: Duplicates list was empty');
        }
    }

    /**
     * Assign the driver to a random session from the options
     *
     * @param $driver
     * @param $options
     */
    protected function assignDriverToRandomSession($driver, $options)
    {
        // Options is keyed by sessionID, so grab a random key
        $this->assignDriverToRandomSlot($driver, array_rand($options));
    }

    /**
     * Assign the driver to a random slot in the given session
     *
     * @param $driver
     * @param $sessionID
     *
     * @throws AssignSlotFailed
     */
    protected function assignDriverToRandomSlot($driver, $sessionID)
    {
        $options = $this->getOptionsFor($sessionID, $driver);
        $optKey = array_rand($options);
        if ($optKey !== NULL) {
            $this->assignDriverToSessionSlot($driver, $sessionID, $options[$optKey]);
        } else {
            throw new AssignSlotFailed('Random Slot: Option list was empty');
        }
    }

    /**
     * Assign the driver to the specified session slot
     *
     * @param $driver
     * @param $sessionID
     * @param $slot
     */
    protected function assignDriverToSessionSlot($driver, $sessionID, $slot)
    {
        if ($slot == 'SO') {
            // Sit out this session
            $this->sessions[$sessionID]['sitOuts'][] = $driver;
            // Update the sitout count for the driver
            $this->drivers[$driver]['sitouts']++;
        } else {
            $this->sessions[$sessionID]['slots'][$slot][] = $driver;
            // Update the list of slots taken by this driver
            $this->drivers[$driver]['slots'][] = $slot;
        }
        // Update the list of sessions taken by this driver
        $this->drivers[$driver]['sessions'][] = $sessionID;
    }

}
