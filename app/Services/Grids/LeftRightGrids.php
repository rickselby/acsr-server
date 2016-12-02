<?php

namespace App\Services\Grids;

use App\Contracts\GridsContract;
use App\Services\Grids\LeftRightGrids\GenerateGrids;

/*
 * LeftRight grids. Really restrictive on the possible numbers, it's all I can wrap
 * my head around right now.
 *
 * Each driver must race an even number of heats.
 *
 * Grid size must be twice the number of heats per driver
 *
 * Each driver will start half their heats from one side of the grid, and half from the other.
 */
class LeftRightGrids implements GridsContract
{
    /** @var  int */
    protected $driversPerHeat;

    /** @var  int */
    protected $heatsPerDriver;

    /** @var GenerateGrids */
    protected $generateGrids;

    public function __construct(GenerateGrids $generateGrids)
    {
        $this->generateGrids = $generateGrids;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(int $driversPerHeat, int $heatsPerDriver)
    {
        $this->driversPerHeat = $driversPerHeat;
        $this->heatsPerDriver = $heatsPerDriver;
    }

    /**
     * @inheritdoc
     */
    public function isValid()
    {
        return
            // the number of drivers per heat should be double the number of heats per driver
            ($this->driversPerHeat / $this->heatsPerDriver == 2)
            // the number of heats per driver should be even
            && ($this->heatsPerDriver % 2 == 0);
    }

    /**
     * @inheritdoc
     */
    public function serversNeeded(int $driverCount)
    {
        // round the drivers up to the nearest two
        $drivers = $driverCount + ($driverCount % 2);
        return floor($drivers / $this->driversPerHeat);
    }

    /**
     * @inheritdoc
     */
    public function maxDriversFor(int $serverCount)
    {
        return $serverCount * $this->driversPerHeat;
    }

    /**
     * @inheritdoc
     */
    public function generateGrids(array $drivers)
    {
        return $this->generateGrids->generate($drivers, $this->driversPerHeat, $this->heatsPerDriver);
    }

}