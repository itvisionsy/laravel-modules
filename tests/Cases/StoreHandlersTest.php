<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 10:51 AM
 */

namespace ItvisionSy\Laravel\Modules\Tests\Cases;

use App\Modules\Test\Module as TestModule;
use ItvisionSy\Laravel\Modules\Modules;
use ItvisionSy\Laravel\Modules\StoreHandlers\DummyStoreHandler;
use ItvisionSy\Laravel\Modules\StoreHandlers\SimpleDbStoreHandler;
use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;
use Config;

class StoreHandlersTest extends LaravelModulesTestCase
{

    public function testDummyStoreHandler()
    {

        //config
        Config::set('modules.store_handler', DummyStoreHandler::class);

        //registeration
        $this->artisan('modules:make', ["id" => "Test", "name" => "Test Module", "--url" => "test"]);
        $this->loadModuleFiles("Test");

        //test
        Config::set('modules.modules_enabled_by_default', true);
        TestModule::make()->disableModule();
        $this->assertFalse(TestModule::isDisabled());
        Config::set('modules.modules_enabled_by_default', false);
        TestModule::make()->disableModule();
        $this->assertFalse(TestModule::isEnabled());
    }

    public function testSimpleDbStoreHandler()
    {

        //module
        $this->artisan('modules:make', ["id" => "Test", "name" => "Test Module", "--url" => "test"]);
        $this->loadModuleFiles("Test");

        //config
        touch(static::appPath('/database.sqlite'));
        Modules::setStoreHandler(SimpleDbStoreHandler::make());
        $this->artisan('modules:db:init');

        //test
        Config::set('modules.modules_enabled_by_default', false);
        $this->assertTrue(TestModule::isDisabled());
        $this->assertFalse(TestModule::isEnabled());
        Config::set('modules.modules_enabled_by_default', true);
        $this->assertFalse(TestModule::isDisabled());
        $this->assertTrue(TestModule::isEnabled());
        TestModule::disableModule();
        $this->assertTrue(TestModule::isDisabled());
        $this->assertFalse(TestModule::isEnabled());
        $this->assertEquals(0, count(Modules::enabled()));
        $this->assertEquals(1, count(Modules::disabled()));
        TestModule::enableModule();
        $this->assertFalse(TestModule::isDisabled());
        $this->assertTrue(TestModule::isEnabled());
        $this->assertEquals(1, count(Modules::enabled()));
        $this->assertEquals(0, count(Modules::disabled()));

        //clean up
        static::rm($this->appPath('/database.sqlite'));

    }

}
