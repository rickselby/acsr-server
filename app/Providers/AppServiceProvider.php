<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \App\Contracts\ConfigFileContract::class,
            \App\Services\ConfigFile\BasicConfigFile::class
        );
        $this->app->bind(
            \App\Contracts\GridsContract::class,
            \App\Services\Grids\LeftRightGrids::class
        );
        $this->app->bind(
            \App\Contracts\ServerManagerContract::class,
            \App\Services\ServerManager\ClientServerManager::class
        );
        $this->app->bind(
            \App\Contracts\ServerProviderContract::class,
            \App\Services\ServerProvider\LinodeServerProvider::class
        );
        $this->app->bind(
            \App\Contracts\VoiceServerContract::class,
            \App\Services\VoiceServer\DiscordVoiceServer::class
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
