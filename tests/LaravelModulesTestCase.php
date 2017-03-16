<?php

namespace ItvisionSy\Laravel\Modules\Tests;

use Config;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 7:35 AM
 */
abstract class LaravelModulesTestCase extends \Illuminate\Foundation\Testing\TestCase
{

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface|\Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $this->mockConfig();

        $app->register(\ItvisionSy\Laravel\Modules\ServiceProvider::class);

        return $app;
    }

    protected static function rm($path)
    {
        if (is_dir($path)) {
            $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($path);
        } elseif (is_file($path)) {
            unlink($path);
        }
    }

    protected static function appPath($path = '')
    {
        return preg_replace('#' . DIRECTORY_SEPARATOR . '+#', DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, __DIR__ . '/../app/' . $path));
    }

    protected static function modulesPath($path = '')
    {
        return static::appPath('modules/' . $path);
    }

    public function setUp()
    {
        parent::setUp();
        mkdir(static::appPath());
        $this->mockConfig();
    }

    public function tearDown()
    {
        static::rm(static::appPath());
        parent::tearDown();
    }

    protected function mockConfig()
    {
        Config::set('modules.directory', $this->modulesPath());
        Config::set('database.connections.sqlite.database', $this->appPath('/database.sqlite'));
    }

    protected function loadModuleFiles($name)
    {
        require_once static::modulesPath("/{$name}/Module.php");
        require_once static::modulesPath("/{$name}/Http/Controllers/WelcomeController.php");
    }

}