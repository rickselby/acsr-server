<?php

namespace App\Services\Grids;

use App\Contracts\GridGeneratorContract;
use App\Services\Grids\EachSlotGrids\GenerateGrids;

/*
 * EachSlot grids.
 *
 * Each driver will start from each grid slot, as many heats as necessary.
 *
 * No more than 6 drivers per heat though.
 */
class EachSlotGrids implements GridGeneratorContract
{
    private $maxHeatSize = 8;

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
        return ceil($driverCount / $this->maxHeatSize);
    }

    /**
     * @inheritdoc
     */
    public function maxDriversFor(int $serverCount)
    {
        return $serverCount * $this->maxHeatSize;
    }

    /**
     * @inheritdoc
     */
    public function generateGrids(array $drivers)
    {
        return $this->generateGrids->generate($drivers, $this->serversNeeded(count($drivers)));
    }

}