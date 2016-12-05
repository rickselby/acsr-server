<?php

namespace App\Services\ServerManager;

use App\Contracts\ServerManagerContract;
use App\Models\LinodeServer;
use App\Services\ServerManager\Linode\Datacenters;
use App\Services\ServerManager\Linode\Linode;

class LinodeServerManager implements ServerManagerContract
{
    /** @var Linode */
    protected $linode;#
    /** @var Datacenters */
    protected $datacenters;

    public function __construct(Linode $linode, Datacenters $datacenters)
    {
        $this->linode = $linode;
        $this->datacenters = $datacenters;
    }

    /**
     * Create a new Assetto Corsa server; return an identifier
     * @return int|false
     */
    public function create(int $eventID)
    {
        $server = $this->linode->create($this->datacenters->getNextDatacenterFor($eventID));

        $server = LinodeServer::create([
            'linode_id' => $server['linodeID'],
            'ip' => $server['ip'],
            'password' => $server['password'],
        ]);

        return $server->id;
    }

    /**
     * Set the config file for the given server
     * @param int $serverID
     * @param string $configFile
     * @return bool
     */
    public function setConfig(int $serverID, string $configFile)
    {
        // TODO: Implement setConfig() method.
    }

    /**
     * Set the entry list for the given server
     * @param int $serverID
     * @param string $entryListFile
     * @return bool
     */
    public function setEntryList(int $serverID, string $entryListFile)
    {
        // TODO: Implement setEntryList() method.
    }

    /**
     * Start a server
     * @param int $serverID
     * @return bool
     */
    public function start(int $serverID)
    {
        // TODO: Implement start() method.
    }

    /**
     * Stop a server
     * @param int $serverID
     * @return bool
     */
    public function stop(int $serverID)
    {
        // TODO: Implement stop() method.
    }

    /**
     * Get the results from a server
     * @param int $serverID
     * @return string Assetto Corsa Results File
     */
    public function getResults(int $serverID)
    {
        // TODO: Implement getResults() method.
    }

    /**
     * Get the (assetto corsa server) log from a server
     * @param int $serverID
     * @return string
     */
    public function getLog(int $serverID)
    {
        // TODO: Implement getLog() method.
    }

    /**
     * Destroy a server
     * @param int $serverID
     * @return bool
     */
    public function destroy(int $serverID)
    {
        $server = LinodeServer::find($serverID);
        $this->linode->destroy($server->linode_id);
    }
}