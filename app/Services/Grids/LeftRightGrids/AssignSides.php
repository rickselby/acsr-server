<?php

namespace App\Services\Grids\LeftRightGrids;

class AssignSides
{
    const LEFT = 1;
    const RIGHT = 2;
    const EITHER = 3;

    /** @var array */
    protected $drivers;

    /** @var array */
    protected $races;

    public function assign($races)
    {
        $this->races = $races;
        $this->populateDrivers($races);

        while(!$this->isComplete()) {

            // If there are drivers who only have one grid side left to assign,
            // sort them out first
            if ($this->areDriversToComplete()) {
                $this->completeDrivers();
            } else {
                // No-one to complete, so randomly complete one slot
                $raceID = $this->getIncompleteRaceID();
                $slotID = array_rand($this->races[$raceID]['slots']);
                $this->setGridsFor($raceID, $slotID);
            }

        }

        return $this->races;
    }

    /**
     * Populate the drivers array with drivers found in the races
     *
     * @param $races
     */
    protected function populateDrivers($races)
    {
        foreach($races AS $race) {
            foreach($race['slots'] AS $slot) {
                foreach($slot AS $driver) {
                    if (!isset($this->drivers[$driver])) {
                        $this->drivers[$driver] = [
                            'races' => 0,
                            'left' => 0,
                            'right' => 0,
                        ];
                    }
                    $this->drivers[$driver]['races']++;
                }
            }
        }
    }

    /**
     * Check if all drivers have had all their grid slots assigned
     *
     * @return bool
     */
    protected function isComplete()
    {
        foreach($this->drivers AS $driver) {
            if ($driver['left'] + $driver['right'] != $driver['races']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if there are any drivers that need completing
     *
     * @return int
     */
    protected function areDriversToComplete()
    {
        return count($this->getDriversToComplete());
    }

    /**
     * Get the list of drivers that need to be completed (they have only one grid side
     * left to populate)
     *
     * @return array
     */
    protected function getDriversToComplete()
    {
        $drivers = [];

        foreach($this->drivers AS $driverID => $driver) {
            if (
                $driver['left'] + $driver['right'] != $driver['races']
                &&
                (
                $driver['left'] == ($driver['races'] / 2)
                    ||
                    $driver['right'] == ($driver['races'] / 2)
                )
            ) {
                $drivers[] = $driverID;
            }
        }

        return $drivers;
    }

    /**
     * Get the list of drivers that need completing, find the relevant slots,
     * and set them
     */
    protected function completeDrivers()
    {
        $drivers = $this->getDriversToComplete();

        $toComplete = [];

        foreach($this->races AS $raceID => $race) {
            foreach($race['slots'] AS $slotID => $slot) {
                foreach($drivers AS $driver) {
                    if (in_array($driver, $slot)) {
                        if (!isset($toComplete[$raceID])) {
                            $toComplete[$raceID] = [];
                        }
                        $toComplete[$raceID][] = $slotID;
                    }
                }
            }
        }

        foreach($toComplete AS $raceID => $slots) {
            foreach(array_unique($slots) AS $slotID) {
                $this->setGridsFor($raceID, $slotID);
            }
        }

    }

    /**
     * Set the grid slots for the given race and slot
     *
     * NB: left and right are placeholders, it's more ahead/behind, grids will
     * depend on the track being raced at.
     *
     * @param $raceID
     * @param $slotID
     */
    protected function setGridsFor($raceID, $slotID)
    {
        // To store which driver is on which side
        $left = $right = null;

        // Get the drivers assigned to this slot
        $drivers = $this->races[$raceID]['slots'][$slotID];

        // See if either driver needs to be on one side
        foreach($drivers AS $id => $driver) {
            switch($this->whichSide($driver)) {
                case self::LEFT:
                    $left = $driver;
                    unset($drivers[$id]);
                    break 2;
                case self::RIGHT:
                    $right = $driver;
                    unset($drivers[$id]);
                    break 2;
                default:
                    // try the other driver
            }
        }

        // If neither driver need to be on one side, randomly assign one to the left
        if (!$left && !$right) {
            $key = array_rand($drivers);
            $left = $drivers[$key];
            unset($drivers[$key]);
        }

        // Now, set the other driver to the other side
        if ($left) {
            $right = array_pop($drivers);
        } else {
            $left = array_pop($drivers);
        }

        // Set up the grid slots based on the slot number
        $grid['left'] = $slotID * 2;
        $grid['right'] = $grid['left'] - 1;

        // Set the drivers on the grids
        $this->races[$raceID]['grid'][$grid['left']] = $left;
        $this->races[$raceID]['grid'][$grid['right']] = $right;

        // Mark that these drivers have been on this side of the grid again
        $this->drivers[$left]['left']++;
        $this->drivers[$right]['right']++;

        // Unset the slot so it doesn't get processed again
        unset($this->races[$raceID]['slots'][$slotID]);
    }

    /**
     * Work out which side a driver can be
     *
     * @param $driver
     *
     * @return int|null
     */
    protected function whichSide($driver)
    {
        $leftFull = ($this->drivers[$driver]['left'] == $this->drivers[$driver]['races'] / 2);
        $rightFull = ($this->drivers[$driver]['right'] == $this->drivers[$driver]['races'] / 2);

        if (!$leftFull && !$rightFull) {
            return self::EITHER;
        } elseif (!$leftFull) {
            return self::LEFT;
        } elseif (!$rightFull) {
            return self::RIGHT;
        }
        return null;
    }

    /**
     * Randomly pick a race ID that has slots left to process
     *
     * @return int
     */
    protected function getIncompleteRaceID()
    {
        $raceIDs = [];
        foreach($this->races AS $raceID => $race) {
            if (count($race['slots'])) {
                $raceIDs[$raceID] = $raceID;
            }
        }
        return array_rand($raceIDs);
    }

}
