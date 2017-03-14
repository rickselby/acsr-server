<?php

namespace App\Services;

use App\Contracts\GridGeneratorContract;
use App\Models\Event;
#use App\Services\Grids\EachSlotGrids;
use App\Services\Grids\LeftRightGrids;

class GridService
{
    protected $gridGenerators = [];
    protected $defaultGridGenerator = [];

    /** @var Event */
    private $event;

    /**
     * Build the list of available grid generators
     * Could be done in a config file, I guess...?
     */
    public function __construct()
    {
        $this->setDefaultGridGenerator(LeftRightGrids::class);
#        $this->addGridGenerator(range(1, 14), EachSlotGrids::class);
    }

    /**
     * Set the event to generate grids for
     *
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        // Save the event
        $this->event = $event;
    }

    /**
     * Get a count of how many servers are needed for the given number of drivers
     *
     * @return int
     */
    public function serversNeeded()
    {
        return $this->getGridGenerator()->serversNeeded($this->event->signups->count());
    }

    /**
     * Get the maximum number of drivers possible given the number of servers
     *
     * @param int $serverCount
     *
     * @return int
     */
    public function maxDriversFor(int $serverCount)
    {
        return $this->getGridGenerator()->maxDriversFor($serverCount);
    }

    /**
     * Generate grids for the given drivers
     * @param array $drivers
     * @return array
     */
    public function generateGrids(array $drivers)
    {
        return $this->getGridGenerator()->generateGrids($drivers);
    }

    /**
     * Set the default grid generator to use
     *
     * @param $generator
     */
    private function setDefaultGridGenerator($generator)
    {
        $this->defaultGridGenerator = $generator;
    }

    /**
     * Set another grid generator for a different amount of drivers
     *
     * @param $range
     * @param $generator
     */
    private function addGridGenerator($range, $generator)
    {
        foreach($range AS $drivers) {
            $this->gridGenerators[$drivers] = $generator;
        }
    }

    /**
     * Get the correct grid generator for the current amount of signups
     * @return GridGeneratorContract
     */
    private function getGridGenerator()
    {
        return app(
            $this->gridGenerators[$this->event->signups->count()]
                ?? $this->defaultGridGenerator
        );
    }

}
