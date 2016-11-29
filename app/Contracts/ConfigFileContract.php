<?php

namespace App\Contracts;

interface ConfigFileContract
{
    /**
     * Set default config values
     * @param array $options
     * @return bool
     */
    public function setServerConfig(array $options);

    /**
     * Get the server config with the given options
     * @param array $options
     * @return string
     */
    public function getServerConfig(array $options);

    /**
     * Get an entry list for the given drivers
     * @param array $drivers
     * @return string
     */
    public function getEntryList(array $drivers);
}