<?php

namespace App\Contracts;

interface ConfigFileContract
{
    /**
     * Update the server config with the given options
     * @param string $config
     * @param array $options
     * @return string
     */
    public function alterServerConfig($config, array $options);

    /**
     * Get an entry list for the given drivers
     * @param string $carModel
     * @param array $drivers
     * @return string
     */
    public function getEntryList($carModel, array $drivers);
}