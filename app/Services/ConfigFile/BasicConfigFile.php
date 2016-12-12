<?php

namespace App\Services\ConfigFile;

use App\Contracts\ConfigFileContract;

class BasicConfigFile implements ConfigFileContract
{

    /**
     * Take the server config and replace key in $options with the given values
     *
     * @param string $config
     * @param array $options
     * @return string
     */
    public function alterServerConfig($config, array $options)
    {
        // Break the config into lines
        $configLines = explode("\n", $config);
        foreach($configLines AS $id => $configLine) {
            // Break each line into parts
            $lineParts = explode('=', $configLine);
            // Not all lines have an equals...
            if (count($lineParts) == 2) {
                // Check if we need to overwrite this value with a new value from $options
                if (in_array($lineParts[0], array_keys($options))) {
                    $lineParts[1] = $options[$lineParts[0]];
                }
                // Re-combine the parts for the line
                $configLines[$id] = implode('=', $lineParts);
            }
        }
        // Return the altered config
        return implode("\n", $configLines);
    }

    /**
     * Get an entry list for the given drivers
     * @param array $drivers
     * @return string
     */
    public function getEntryList($carModel, array $drivers)
    {
        $entryList = [];

        $count = 0;
        foreach($drivers AS $driver) {
            $entryList[] = '[CAR_'.$count.']';
            $entryList[] = 'MODEL='.$carModel;
            $entryList[] = 'SKIN='.$driver->skin;
            $entryList[] = 'SPECTATOR_MODE=0';
            $entryList[] = 'DRIVERNAME='.$driver->name;
            $entryList[] = 'TEAM=';
            $entryList[] = 'GUID='.$driver->steam_guid;
            $entryList[] = 'BALLAST='.($driver->ballast ?: '0');
            $entryList[] = '';
        }

        return implode("\n", $entryList);
    }
}