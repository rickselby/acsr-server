<?php

namespace App\Services\ServerProvider;

use App\Contracts\ServerProviderContract;
use App\Models\Event;
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
    public function create(Event $event)
    {
        $dataCenterID = $this->datacenters->getNextDatacenterFor($event->id);
        $linodeServer = $this->linode->create($dataCenterID);

        $server = new Server();
        $server->fill([
            'provider_id' => $linodeServer['linodeID'],
            'ip' => $linodeServer['ip'],
            'password' => $linodeServer['password'],
            'settings' => [
                'datacenter_id' => $dataCenterID,
            ],
        ]);
        $event->servers()->save($server);

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
