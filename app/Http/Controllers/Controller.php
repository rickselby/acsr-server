<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    use AuthorizesRequests {
        resourceAbilityMap AS authorizesResourcesResourceAbilityMap;
    }

    /**
     * A list of resource abilities to overwrite / append to the resource ability map
     * @var array
     */
    protected $resourceAbilityMap = [];

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        // Merge with the class variable to allow easier changes / additions
        return array_merge(
            $this->authorizesResourcesResourceAbilityMap(),
            $this->resourceAbilityMap
        );
    }
}
