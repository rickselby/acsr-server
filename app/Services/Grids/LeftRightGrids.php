<?php

namespace App\Services\Grids;

use App\Contracts\GridGeneratorContract;
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
class LeftRightGrids implements GridGeneratorContract
{
    /** @var  int */
    private $driversPerHeat = 8;

    /** @var  int */
    private $heatsPerDriver = 4;

    /** @var GenerateGrids */
    protected $generateGrids;

    public function __construct(GenerateGrids $generateGrids)
    {
        $this->generateGrids = $generateGrids;
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
        // 2 servers would be used at (heatsize * 2) drivers, so one server would be 2 drivers short of that...
        return (($serverCount + 1) * $this->driversPerHeat) - 2;
    }

    /**
     * @inheritdoc
     */
    public function generateGrids(array $drivers)
    {
        return $this->generateGrids->generate($drivers, $this->driversPerHeat, $this->heatsPerDriver);
    }

}