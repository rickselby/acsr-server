<?php

namespace App\Contracts;

interface ConfigFileContract
{
    /**
     * Update the server config with the given options
     * @param array $options
     * @return string
     */
    public function alterServerConfig($config, array $options);

    /**
     * Get an entry list for the given drivers
     * @param array $drivers
     * @return string
     */
    public function getEntryList(array $drivers);
}