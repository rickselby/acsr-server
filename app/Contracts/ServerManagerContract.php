<?php

namespace App\Contracts;

/**
 * Contract for creating and managing an Assetto Corsa server.
 */
interface ServerManagerContract
{
    /**
     * Create a new Assetto Corsa server; return an identifier
     * @return int|false
     */
    public function create();

    /**
     * Set the config file for the given server
     * @param int $serverID
     * @param string $configFile
     * @return bool
     */
    public function setConfig(int $serverID, string $configFile);

    /**
     * Set the entry list for the given server
     * @param int $serverID
     * @param string $entryListFile
     * @return bool
     */
    public function setEntryList(int $serverID, string $entryListFile);

    /**
     * Start a server
     * @param int $serverID
     * @return bool
     */
    public function start(int $serverID);

    /**
     * Stop a server
     * @param int $serverID
     * @return bool
     */
    public function stop(int $serverID);

    /**
     * Get the results from a server
     * @param int $serverID
     * @return string Assetto Corsa Results File
     */
    public function getResults(int $serverID);

    /**
     * Get the (assetto corsa server) log from a server
     * @param int $serverID
     * @return string
     */
    public function getLog(int $serverID);

    /**
     * Destroy a server
     * @param int $serverID
     * @return bool
     */
    public function destroy(int $serverID);
}