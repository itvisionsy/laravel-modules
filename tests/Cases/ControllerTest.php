<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 9:19 AM
 */

namespace ItvisionSy\Laravel\Modules\Tests\Cases;

use Config;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollection;
use ItvisionSy\Laravel\Modules\Controller;
use ItvisionSy\Laravel\Modules\Tests\LaravelModulesTestCase;

class ControllerTest extends LaravelModulesTestCase
{

    public $baseUrl = 'http://localhost:8899/';

    public function testController()
    {
        //registeration
        $this->artisan('modules:make', ["id" => "Test", "name" => "Test Module", "--url" => "test"]);
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

}
