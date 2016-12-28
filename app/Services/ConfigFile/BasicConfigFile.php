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

        // Start with an empty section
        $section = '';

        foreach($configLines AS $id => $configLine) {

            // Break each line into parts
            $lineParts = explode('=', $configLine);
            // Not all lines have an equals...
            if (count($lineParts) == 2) {
                $key = $section.'.'.$lineParts[0];
                // Check if we need to overwrite this value with a new value from $options
                if (in_array($key, array_keys($options))) {
                    $lineParts[1] = $options[$key];
                }
                // Re-combine the parts for the line
                $configLines[$id] = implode('=', $lineParts);
            }

            // OK, so not a key; what about a section?
            if (preg_match('/\[(.*)\]/', $configLine, $matches)) {
                $section = $matches[1];
            }
        }
        // Return the altered config
        return implode("\n", $configLines);
    }

    /**
     * Get an entry list for the given drivers
     *
     * @param string $carModel
     * @param array $drivers Keyed by grid slot
     *
     * @return string
     */
    public function getEntryList($carModel, array $drivers)
    {
        $entryList = [];

        for($i = 1; $i <= max(array_keys($drivers)); $i++) {
            $driver = $drivers[$i] ?? null;

            $entryList[] = '[CAR_'.($i-1).']';
            $entryList[] = 'MODEL='.$carModel;
            $entryList[] = 'SKIN='.($driver ? $driver->skin : '');
            $entryList[] = 'SPECTATOR_MODE=0';
            $entryList[] = 'DRIVERNAME='.($driver ? $driver->user->name : 'blank');
            $entryList[] = 'TEAM=';
            $entryList[] = 'GUID='.($driver ? $driver->user->getProvider('steam')->provider_user_id : '');
            $entryList[] = 'BALLAST='.($driver ? ($driver->ballast ?: '0') : '0');
            $entryList[] = '';
        }

        return implode("\n", $entryList);
    }
}