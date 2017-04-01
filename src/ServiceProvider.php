<?php

/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 1:32 AM
 */

namespace ItvisionSy\Laravel\Modules;

use Artisan;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ItvisionSy\Laravel\Modules\Commands\InitiateDatabaseTable;
use ItvisionSy\Laravel\Modules\Commands\MakeModule;

class ServiceProvider extends BaseServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

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

    public function boot() {

        //merge the config
        $this->mergeConfigFrom(__DIR__ . join(DIRECTORY_SEPARATOR, ['', '..', 'config', 'defaults.php']), 'modules');

        //load the modules
        $modules = Modules::enabled();
        $publishesMigrations = [];
        /** @var Module[]|array $modules */
        foreach ($modules as $module) {
            $routesPath = $module->routesPath();
            if ($routesPath) {
                if (method_exists($this, 'loadRoutesFrom')) {
                    $this->loadRoutesFrom($routesPath);
                } else {
                    $module->registerRoutes($this->app);
                }
            }
            if (($moduleViewsPath = $module->viewsPath())) {
                if (method_exists($this, 'loadViewsFrom')) {
                    $this->loadViewsFrom($moduleViewsPath, $module->id());
                } else {
                    $module->registerViewsPath($this->app);
                }
            }
            if (($moduleMigrationsPath = $module->migrationsPath())) {
//                $this->thisLoadMigrationsFrom($moduleMigrationsPath);
                $publishesMigrations[$moduleMigrationsPath] = database_path('migrations');
            }
        }

        //publishes migration
        $this->publishes($publishesMigrations, 'migrations');
    }

    protected function thisLoadMigrationsFrom($paths) {
        if (method_exists($this, 'loadMigrationsFrom')) {
            $this->loadMigrationsFrom($path);
        } else {
            $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
                foreach ((array) $paths as $path) {
                    $migrator->path($path);
                }
            });
        }
    }

}
