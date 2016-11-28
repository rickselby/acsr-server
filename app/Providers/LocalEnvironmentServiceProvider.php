<?php

/**
 * http://stackoverflow.com/questions/29591887/laravel-add-alias-only-for-local-environment
 */
namespace App\Providers;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class LocalEnvironmentServiceProvider extends ServiceProvider
{
    /**
     * List of Local Environment Providers
     * @var array
     */
    protected $localProviders = [
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        \Barryvdh\Debugbar\ServiceProvider::class,
    ];

    /**
     * List of only Local Environment Facade Aliases
     * @var array
     */
    protected $facadeAliases = [
        'Debugbar' => \Barryvdh\Debugbar\Facade::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            $this->registerServiceProviders();
            $this->registerFacadeAliases();

            \Route::group(['middleware' => ['web']], function () {
                \Route::get('/login/dev', function () {
                    \Auth::loginUsingID(1);
                    return \Redirect::to('/');
                });
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Load local service providers
     */
    protected function registerServiceProviders() {
        foreach ($this->localProviders as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Load additional Aliases
     */
    public function registerFacadeAliases() {
        $loader = AliasLoader::getInstance();
        foreach ($this->facadeAliases as $alias => $facade) {
            $loader->alias($alias, $facade);
        }
    }
}