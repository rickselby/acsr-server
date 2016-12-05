<?php

namespace App\Services\ServerManager\Linode;

use App\Models\LinodeServer;
use Linode\AvailApi;

class Datacenters
{
    /**
     * The list of datacenters to use
     */
    protected $datacenters = [
        'london',
        'atlanta',
    ];

    /**
     * Get a list of datacenter IDs
     *
     * @return int[]
     */
    protected function getDatacenters()
    {
        $datacenters = [];
        $availApi = new AvailApi(env('LINODE_API_KEY'));
        foreach($availApi->dataCenters() AS $dataCenter) {
            if (in_array($dataCenter['ABBR'], $this->datacenters)) {
                $datacenters[] = $dataCenter['DATACENTERID'];
            }
        }

        return $datacenters;
    }

    /**
     * Get the datacenterID that should be next used for the given event
     *
     * @param int $eventID
     *
     * @return int
     */
    public function getNextDatacenterFor($eventID)
    {
        // Key an array of datacenters by datacenterID
        $datacenters = [];
        foreach($this->getDatacenters() AS $datacenterID) {
            $datacenters[$datacenterID] = 0;
        }

        // Count which datacenters are in use for this event
        foreach(LinodeServer::forEvent($eventID) AS $server) {
            $datacenters[$server['datacenter_id']]++;
        }

        if (count(array_unique($datacenters)) == 1) {
            // get a random datacenter
            return array_rand(array_flip($this->getDatacenters()));
        } else {
            // Sort so the smallest number is at the start
            asort($datacenters);
            // Get the key (datacenterID) of the first item in the array
            reset($datacenters);
            return key($datacenters);
        }

    }
}
