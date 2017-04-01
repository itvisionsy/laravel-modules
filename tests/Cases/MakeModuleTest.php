<?php

namespace ItvisionSy\Laravel\Modules\Tests\Cases;

use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;

class MakeModuleTest extends LaravelModulesTestCase
{

    public function testSimpleCommand()
    {
        $this->artisan('make:module', ["id" => "Test", "name" => "Test Module"]);
        $this->loadModuleFiles("Test");
        $this->assertDirectoryExists($this->modulesPath());
        $this->assertDirectoryExists($this->modulesPath('Test/'));
        $this->assertDirectoryExists($this->modulesPath('Test/Http/'));
        $this->assertDirectoryExists($this->modulesPath('Test/Http/Controllers/'));
        $this->assertDirectoryExists($this->modulesPath('Test/Views/'));
        $this->assertDirectoryExists($this->modulesPath('Test/Models/'));
        $this->assertFileExists($this->modulesPath('Test/' . config('modules.class_name') . '.php'));
        $this->assertFileExists($this->modulesPath('Test/Http/routes.php'));
        $this->assertTrue(class_exists(\App\Modules\Test\Module::class));
        $this->assertEquals(\App\Modules\Test\Module::make()->id(), "Test");
        $this->assertEquals(\App\Modules\Test\Module::make()->name(), "Test Module");
        $this->assertEquals(\App\Modules\Test\Module::make()->urlPrefix(), "test");
        $this->assertEquals(\App\Modules\Test\Module::make()->routePrefix(), "test");
    }

    public function testCompoundName()
    {
        $this->artisan('make:module', ["id" => "TestModule", "name" => "Test Module"]);
        $this->loadModuleFiles("TestModule");
        $this->assertDirectoryExists($this->modulesPath('TestModule/'));
        $this->assertFileExists($this->modulesPath('TestModule/' . config('modules.class_name') . '.php'));
        $this->assertTrue(class_exists(\App\Modules\TestModule\Module::class));
        $this->assertEquals(\App\Modules\TestModule\Module::make()->id(), "TestModule");
        $this->assertEquals(\App\Modules\TestModule\Module::make()->urlPrefix(), "testmodule");
        $this->assertEquals(\App\Modules\TestModule\Module::make()->routePrefix(), "testmodule");
    }

    public function testCustomUrl()
    {
        $this->artisan('make:module', ["id" => "TestModuleWithUrl", "name" => "Test Module", "--url" => "test-module"]);
        $this->loadModuleFiles("TestModuleWithUrl");
        $this->assertDirectoryExists($this->modulesPath('TestModuleWithUrl/'));
        $this->assertFileExists($this->modulesPath('TestModuleWithUrl/' . config('modules.class_name') . '.php'));
        $this->assertTrue(class_exists(\App\Modules\TestModuleWithUrl\Module::class));
        $this->assertEquals(\App\Modules\TestModuleWithUrl\Module::make()->id(), "TestModuleWithUrl");
        $this->assertEquals(\App\Modules\TestModuleWithUrl\Module::make()->urlPrefix(), "test-module");
        $this->assertEquals(\App\Modules\TestModuleWithUrl\Module::make()->routePrefix(), "test-module");
    }

}
