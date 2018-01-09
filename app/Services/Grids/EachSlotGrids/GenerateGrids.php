<?php

namespace App\Services\Grids\EachSlotGrids;

class GenerateGrids
{
    /** @var AssignSlots */
    protected $assignSlots;

    /** @var AssignRaces */
    protected $assignRaces;

    /** @var AssignSides */
    protected $assignSides;

    public function __construct(
        AssignSlots $assignSlots,
        AssignRaces $assignRaces,
        AssignSides $assignSides
    ) {
        $this->assignSlots = $assignSlots;
        $this->assignRaces = $assignRaces;
        $this->assignSides = $assignSides;
    }

    public function generate(array $drivers, int $servers)
    {
        // Put drivers into sessions and slots
        $slots = $this->assignSlots->assign(count($drivers), $servers);

        // Split the sessions into individual races
        $raceSlots = $this->assignRaces->assign($slots);

        // Assign each driver an even number of each side of the grid
        $races = $this->assignSides->assign($raceSlots);

        // Resort the races by session
        usort($races, function($a, $b) {
            return $a['session'] - $b['session'];
        });

        // Randomly assign drivers to the numbers used in the grids
        return $this->assignDrivers($drivers, $races);
    }

    /**
     * Assign the drivers to the races
     *
     * @param $drivers
     * @param $races
     *
     * @return mixed
     */
    public function assignDrivers($drivers, $races)
    {
        shuffle($drivers);
        // Arrays start at zero, so push null into the #0 driver
        array_unshift($drivers, null);

        foreach($races AS $raceID => $race) {
            foreach($race['grid'] AS $grid => $driver) {
                if (isset($drivers[$driver])) {
                    $races[$raceID]['grid'][$grid] = $drivers[$driver];
                } else {
                    unset($races[$raceID]['grid'][$grid]);
                }
            }
        }

        return $races;
    }

}