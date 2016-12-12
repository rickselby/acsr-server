<?php

namespace App\Services\ServerProvider;

use App\Contracts\ServerProviderContract;
use App\Models\Server;
use App\Services\ServerProvider\Linode\Datacenters;
use App\Services\ServerProvider\Linode\Linode;

class LinodeServerProvider implements ServerProviderContract
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
        $dataCenterID = $this->datacenters->getNextDatacenterFor($eventID);
        $server = $this->linode->create($dataCenterID);

        $server = Server::create([
            'provider_id' => $server['linodeID'],
            'ip' => $server['ip'],
            'password' => $server['password'],
            'settings' => [
                'datacenter_id' => $dataCenterID,
            ],
        ]);

        return $server->id;
    }

    /**
     * Destroy a server
     * @param int $serverID
     * @return bool
     */
    public function destroy(Server $server)
    {
        $this->linode->destroy($server->provider_id);
    }
}
