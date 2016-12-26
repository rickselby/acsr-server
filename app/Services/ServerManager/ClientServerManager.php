<?php

namespace App\Services\ServerManager;

use App\Contracts\ServerManagerContract;
use App\Models\Server;
use App\Services\ServerManager\Client\ClientAPI;

class ClientServerManager implements ServerManagerContract
{
    /** @var ClientAPI */
    protected $client;

    public function __construct(ClientAPI $clientAPI)
    {
        $this->client = $clientAPI;
    }

    /**
     * Set the config file for the given server
     * @param Server $server
     * @param string $configFile
     * @return bool
     */
    public function setConfig(Server $server, string $configFile)
    {
        return $this->client->setConfig($server, $configFile);
    }

    /**
     * Set the entry list for the given server
     * @param Server $server
     * @param string $entryListFile
     * @return bool
     */
    public function setEntryList(Server $server, string $entryListFile)
    {
        return $this->client->setEntryList($server, $entryListFile);
    }

    /**
     * Start a server
     * @param Server $server
     * @return bool
     */
    public function start(Server $server)
    {
        return $this->client->start($server);
    }

    /**
     * Stop a server
     * @param Server $server
     * @return bool
     */
    public function stop(Server $server)
    {
        return $this->client->stop($server);
    }

    /**
     * Check if the given server is up and running and ready
     *
     * @param Server $server
     *
     * @return bool
     */
    public function isAvailable(Server $server)
    {
        return $this->client->ping($server);
    }

    /**
     * Get the results from a server
     * @param Server $server
     * @return string Assetto Corsa Results File
     */
    public function getResults(Server $server)
    {
        return $this->client->latestResults($server);
    }

    /**
     * Get the (assetto corsa server) log from a server
     * @param Server $server
     * @return string
     */
    public function getLog(Server $server)
    {
        return $this->client->serverLog($server);
    }

}
