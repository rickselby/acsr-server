<?php

namespace App\Contracts;
use App\Models\Server;

/**
 * Contract for creating and managing an Assetto Corsa server.
 */
interface ServerManagerContract
{
    /**
     * Set the config file for the given server
     * @param Server $server
     * @param string $configFile
     * @return bool
     */
    public function setConfig(Server $server, string $configFile);

    /**
     * Set the entry list for the given server
     * @param Server $server
     * @param string $entryListFile
     * @return bool
     */
    public function setEntryList(Server $server, string $entryListFile);

    /**
     * Start a server
     * @param Server $server
     * @return bool
     */
    public function start(Server $server);

    /**
     * Stop a server
     * @param Server $server
     * @return bool
     */
    public function stop(Server $server);

    /**
     * Get the results from a server
     * @param Server $server
     * @return string Assetto Corsa Results File
     */
    public function getResults(Server $server);

    /**
     * Get the (assetto corsa server) log from a server
     * @param Server $server
     * @return string
     */
    public function getLog(Server $server);

}
