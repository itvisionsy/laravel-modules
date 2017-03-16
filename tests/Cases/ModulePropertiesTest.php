<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 12:20 PM
 */

namespace ItvisionSy\Laravel\Modules\Tests\Cases;

use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;

class ModulePropertiesTest extends LaravelModulesTestCase
{

    public function testModuleProperties()
    {
        $this->artisan('modules:make', ["id" => "Test", "name" => "Test Module"]);
        $this->loadModuleFiles("Test");
        $module = \App\Modules\Test\Module::make();
        $this->assertEquals('Test', $module->id());
        $this->assertEquals('Test Module', $module->name());
        $this->assertEquals('test', $module->urlPrefix());
        $this->assertEquals('test', $module->routePrefix());

    }
}
