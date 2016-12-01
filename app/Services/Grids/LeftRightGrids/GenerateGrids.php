<?php

namespace App\Services\Grids\LeftRightGrids;

class GenerateGrids
{
    /** @var  AssignSlots */
    protected $assignSlots;

    public function __construct(AssignSlots $assignSlots)
    {
        $this->assignSlots = $assignSlots;
    }

    public function generate(array $drivers, $driversPerHeat, $heatsPerDriver)
    {
        // Put drivers into sessions and slots
        $slots = $this->assignSlots->assign(count($drivers), $driversPerHeat, $heatsPerDriver);

        // ...continue...!
    }

}