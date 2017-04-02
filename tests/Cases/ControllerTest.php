<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 9:19 AM
 */

namespace ItvisionSy\Laravel\Modules\Tests\Cases;

use Exception;
use ItvisionSy\Laravel\Modules\Modules;
use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;

class ControllerTest extends LaravelModulesTestCase
{

    public $baseUrl = 'http://localhost:8899/';

    public function testController()
    {
        //registeration
        $this->artisan('make:module', ["id" => "Test", "name" => "Test Module", "--url" => "test"]);
        $this->loadModuleFiles("Test");
        \App\Modules\Test\Module::make()->registerFrameworkResources($this->app);

        //tests
        //testing view and routing
        $response = $this->call('GET', 'test');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Module Test Module', $response->content());

        //testing url generating
        file_put_contents($this->modulesPath() . 'Test/Views/index.blade.php', '{{$this_module->getPathForRoute("index")}}');
        $response = $this->call('GET', 'test');
        $this->assertEquals('http://localhost:8899/test', $response->content());
    }

    public function testInvalidControllers() {
        $this->artisan('make:module', ["id" => "Test", "name" => "Test Module"]);

        //Incorrect namespace
        file_put_contents(rtrim(Modules::modulesDirectory(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "Test" . DIRECTORY_SEPARATOR . "Http" . DIRECTORY_SEPARATOR . "Controllers" . DIRECTORY_SEPARATOR . "WelcomeController.php", "<?php class WelcomeController extends ItvisionSy\Laravel\Modules\Controller { }");
        $this->loadModuleFiles('Test');
        Modules::refreshModules();
        try {
            $controller = new \App\Modules\Test\Http\Controllers\WelcomeController();
            $controller->module();
        } catch (Exception $e) {
            $this->assertEquals("Module controllers should exist inside module root folder", $e->getMessage());
        }

        //Missing module file
        $this->rm(rtrim(Modules::modulesDirectory(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "Test" . DIRECTORY_SEPARATOR . "Http" . DIRECTORY_SEPARATOR . "Controllers" . DIRECTORY_SEPARATOR . "WelcomeController.php");
        $this->loadModuleFiles('Test');
        $this->refreshApplication();
        Modules::refreshModules();
        try {
            $controller = new \App\Modules\Test\Http\Controllers\WelcomeController();
            $controller->module();
        } catch (Exception $e) {
            $this->assertEquals("Module class does not exist", $e->getMessage());
        }
    }

}
