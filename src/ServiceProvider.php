<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 1:32 AM
 */

namespace ItvisionSy\Laravel\Modules;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ItvisionSy\Laravel\Modules\Commands\InitiateDatabaseTable;
use ItvisionSy\Laravel\Modules\Commands\MakeModule;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        //merge the config
        $this->mergeConfigFrom(__DIR__ . join(DIRECTORY_SEPARATOR, ['', '..', 'config', 'defaults.php']), 'modules');

        //load the modules
        $modules = Modules::enabled();
        /** @var Module[]|array $modules */
        foreach ($modules as $module) {
            $routesPath = $module->routesPath();
            if ($routesPath) {
                if (method_exists($this, 'loadRoutesFrom')) {
                    $this->loadRoutesFrom($routesPath);
                } else {
                    if ($this->app->routesAreCached()) {
                        \Artisan::call('cache:clear');
                    }
                    require_once $routesPath;
                }
            }
            if (($moduleViewsPath = $module->viewsPath())) {
                $this->loadViewsFrom($moduleViewsPath, $module->id());
            }
        }
    }

    public function boot()
    {
        //copy config and views to app locations
        $this->publishes([
            __DIR__ . join(DIRECTORY_SEPARATOR, ['', '..', 'config', 'published.php']) => config_path('modules.php')
            //@TODO:allow publishing and reading the stubs for overriding
        ]);
        //registers console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitiateDatabaseTable::class,
                MakeModule::class,
            ]);
        }
    }

}