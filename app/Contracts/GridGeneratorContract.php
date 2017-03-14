<?php

namespace App\Contracts;

/**
 * Contract for generating the grids for the heats
 */
interface GridGeneratorContract
{
    /**
     * Set the options for generating grids
     *
     * @param int $driversPerHeat
     * @param int $heatsPerDriver
     *
     * @return bool
     */
    public function setOptions(
        int $driversPerHeat,
        int $heatsPerDriver
    );

    /**
     * Check the options given to the class can be handled
     * 
     * @return boolean
     */
    public function isValid();

    /**
     * Get a description of how to make the options valid
     *
     * @return string
     */
    public function validDescription();

    /**
     * Get a count of how many servers are needed for the given number of drivers
     * @param int $driverCount
     * @return int
     */
    public function serversNeeded(int $driverCount);

    /**
     * Get the maximum number of drivers possible given the number of servers
     * @param int $serverCount
     * @return int
     */
    public function maxDriversFor(int $serverCount);

    /**
     * Generate grids for the given drivers
     * @param array $drivers
     * @return array
     */
    public function generateGrids(array $drivers);

}
