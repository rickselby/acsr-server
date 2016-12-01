<?php

namespace App\Services\Grids\LeftRightGrids;

class GenerateGrids
{
    /** @var AssignSlots */
    protected $assignSlots;

    /** @var AssignRaces */
    protected $assignRaces;

    public function __construct(AssignSlots $assignSlots, AssignRaces $assignRaces)
    {
        $this->assignSlots = $assignSlots;
        $this->assignRaces = $assignRaces;
    }

    public function generate(array $drivers, $driversPerHeat, $heatsPerDriver)
    {
        // Put drivers into sessions and slots
        $slots = $this->assignSlots->assign(count($drivers), $driversPerHeat, $heatsPerDriver);

        $races = $this->assignRaces->assign($slots);

        // ...continue...!
    }

}