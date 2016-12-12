<?php

namespace App\Services\ServerProvider\Linode;

use Linode\AvailApi;
use Linode\Linode\ConfigApi;
use Linode\Linode\DiskApi;
use Linode\Linode\IpApi;
use Linode\LinodeApi;
use vakata\random\Generator;

class Linode
{
    // List of available datacenters
    const DATACENTER_US = 4;
    const DATACENTER_UK = 7;

    // Size of swap disk
    const SWAPSIZE = 512;

    // Kernel to use
    const KERNEL = 'Latest 64 bit';

    /** @var LinodeApi  */
    protected $linodeApi;
    /** @var DiskApi  */
    protected $diskApi;
    /** @var ConfigApi  */
    protected $configApi;
    /** @var AvailApi  */
    protected $availApi;

    public function __construct()
    {
        if (!env('LINODE_API_KEY')) {
            throw new \Exception('Please set LINODE_API_KEY before using Linode');
        }

        $this->linodeApi = new LinodeApi(env('LINODE_API_KEY'));
        $this->diskApi = new DiskApi(env('LINODE_API_KEY'));
        $this->configApi = new ConfigApi(env('LINODE_API_KEY'));
        $this->availApi = new AvailApi(env('LINODE_API_KEY'));
    }

    /**
     * Create a new linode in the given datacenter
     * @param $dataCentre
     * @return array
     */
    public function create($dataCentre)
    {
        // Create a new linode
        $linode = $this->linodeApi->create($dataCentre, env('LINODE_PLAN_ID'));

        // Get details about the created linode
        $details = $this->linodeApi->getList($linode['LinodeID'])[0];

        // Get the IP address
        $ipApi = new IpApi(env('LINODE_API_KEY'));
        $ipDetails = $ipApi->getList($linode['LinodeID'])[0];

        // Generate a random root password
        $rootPass = Generator::string(32);

        // Create a disk from the stack script
        $diskID = $this->createDisk($linode['LinodeID'], $details['TOTALHD'], $rootPass);

        // Create the config
        $configID = $this->createConfig($linode['LinodeID'], $diskID);

        // Then boot the machine! Yikes!
        $this->linodeApi->boot($linode['LinodeID'], $configID);

        return [
            'linodeID' => $linode['LinodeID'],
            'ip' => $ipDetails['IPADDRESS'],
            'password' => $rootPass,
        ];
    }

    /**
     * Create the new disk from the stackscript that will install everything we need
     *
     * @param $linodeID
     * @param $diskSize
     * @param $rootPassword
     *
     * @return mixed
     */
    protected function createDisk($linodeID, $diskSize, $rootPassword)
    {
        $disk = $this->diskApi->createFromStackScript(
            $linodeID,
            (int) env('LINODE_STACKSCRIPT_ID'),
            json_encode([
                'steam_user' => env('STEAM_LOGIN'),
                'steam_pass' => env('STEAM_PASSWORD'),
            ]),
            (int) env('LINODE_DISTRO_ID'),
            'AC Server',
            $diskSize - self::SWAPSIZE,
            $rootPassword
        );

        return $disk['DiskID'];
    }

    /**
     * Create the config setup for the linode
     *
     * @param int $linodeID
     * @param int $diskID
     *
     * @return mixed
     */
    protected function createConfig($linodeID, $diskID)
    {
        // Create the swap disk
        $swapDisk = $this->diskApi->create(
            $linodeID,
            'SwapDisk',
            'swap',
            self::SWAPSIZE
        );

        $details = $this->configApi->create(
            $linodeID,
            'AC Server',
            $this->getKernelID(),
            // Disk list is comma-separated list of the 9 disks
            implode(',', array_pad([$diskID, $swapDisk['DiskID']], 9, ''))
        );

        return $details['ConfigID'];
    }

    /**
     * Find the kernel whose label starts with SELF::KERNEL
     *
     * @return int
     * @throws \Exception
     */
    protected function getKernelID()
    {
        foreach($this->availApi->kernels() AS $kernel) {
            if (substr($kernel['LABEL'], 0 ,strlen(self::KERNEL)) == self::KERNEL) {
                return $kernel['KERNELID'];
            }
        }
        throw new \Exception('Linode: Could not find Kernel');
    }
    
    /**
     * Delete the given linode
     *
     * @param $linodeID
     */
    public function destroy($linodeID)
    {
        $this->linodeApi->delete($linodeID, true);
    }

    /**
     * Get the list of possible datacenters
     * @return array
     */
    public function getDatacenters()
    {
        return [
            self::DATACENTER_UK,
            self::DATACENTER_US
        ];
    }
}
