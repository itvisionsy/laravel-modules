<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 12:44 PM
 */

namespace ItvisionSy\Laravel\Modules\Tests\Cases;


use Config;
use ItvisionSy\Laravel\Modules\Modules;
use ItvisionSy\Laravel\Modules\StoreHandlers\DummyStoreHandler;
use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;

class GenericTest extends LaravelModulesTestCase
{

    public function testFailStaticAccess()
    {
        $this->expectException(\ErrorException::class);
        Modules::invalidMethod();
    }

    public function testFailedPublicAccess()
    {
        $this->expectException(\ErrorException::class);
        $modules = new Modules();
        $modules->invalidMethod();
    }

    public function testModulesServiceProvider()
    {
        $this->artisan('make:module', ["id" => "Test", "name" => "Test Module"]);
        $this->loadModuleFiles("Test");
        $this->refreshApplication();
        Modules::get('Test');
    }

    public function testCallableHandler()
    {
        $this->refreshApplication();
        Config::set('modules.store_handler', function () {
            return DummyStoreHandler::make();
        });
        Modules::getStoreHandler();
    }

}
