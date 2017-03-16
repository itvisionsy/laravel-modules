<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/14/17
 * Time: 5:57 PM
 */

namespace ItvisionSy\Laravel\Modules;

use Illuminate\Routing\Route;
use ItvisionSy\Laravel\Modules\Interfaces\StaticAndInstanceAccessInterface;
use ItvisionSy\Laravel\Modules\Traits\StaticAndInstanceAccessTrait;
use ItvisionSy\Laravel\Modules\Traits\StaticFactory;

abstract class Module implements StaticAndInstanceAccessInterface
{

    use StaticAndInstanceAccessTrait;
    use StaticFactory;

    static protected $_routes = [];

    protected $moduleId;
    protected $moduleName;
    protected $moduleRouteNamePrefix;
    protected $moduleUrlPrefix;

    public static function grantAccess()
    {
        return ['id', 'name', 'urlPrefix', 'routePrefix',
            'isEnabled', 'isDisabled',
            'disableModule', 'enableModule',
            'modulePath', 'viewsPath', 'getViewName', 'renderView', 'getRoutePath', 'getRouteName', 'getPathForRoute'
        ];
    }

    public function __construct()
    {
        $this->startup();
    }

    /**
     * @return mixed Loads helpers and dependencies
     */
    public function startup()
    {

    }

    /**
     * Returns a path to the routes file, or false if no routes is required
     * @return bool|string
     */
    public function routesPath()
    {
        $defaultPath = $this->modulePath(join(DIRECTORY_SEPARATOR, ['', 'Http', 'routes.php']));
        return file_exists($defaultPath) ? $defaultPath : false;
    }

    /**
     * @return string unique identifier of the module
     */
    protected function id()
    {
        return $this->moduleId;
    }

    /**
     * @return string display name of the module
     */
    protected function name()
    {
        return $this->moduleName;
    }

    /**
     * @return mixed
     */
    protected function routePrefix()
    {
        return $this->moduleRouteNamePrefix ?: $this->id();
    }

    /**
     * @return mixed
     */
    protected function urlPrefix()
    {
        return $this->moduleUrlPrefix ?: $this->id();
    }

    /**
     * @return boolean
     */
    protected function isEnabled()
    {
        return Modules::isModuleEnabled($this);
    }

    /**
     * @return bool
     */
    protected function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * @return mixed
     */
    protected function enableModule()
    {
        return Modules::enableModule($this);
    }

    /**
     * @return mixed
     */
    protected function disableModule()
    {
        return Modules::disableModule($this);
    }

    /**
     * Path relative to module root
     * @param $path
     * @return string
     */
    protected function modulePath($path)
    {
        $class = explode("\\", get_class($this))[2];
        $fullPath = rtrim(config('modules.directory'), "\\") . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . ($path ? $path : "");
        return $fullPath;
    }

    /**
     * @return null|string path to views resources.
     */
    protected function viewsPath()
    {
        $dir = $this->modulePath("Views" . DIRECTORY_SEPARATOR);
        if (is_dir($dir)) {
            return $dir;
        }
        return null;
    }

    /**
     * Appends
     * @param $name
     * @return string
     */
    protected function getViewName($name)
    {
        return $this->id() . "::" . $name;
    }

    /**
     * @param $viewName abstract view name. Key will be automatically added
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderView($viewName, array $data = [], array $mergeData = [])
    {
        return \view($this->getViewName($viewName), $data, $mergeData + ['this_module' => $this, '__this_module' => $this]);
    }

    /**
     * Generate the route path
     * Generates the route path to be used in Route::methods
     * i.e. Route::get(some_generated_path_here)
     * @param $path
     * @return mixed
     */
    protected function getRoutePath($path)
    {
        return trim(str_replace('//', '/', join('/', [config('modules.route_prefix'), $this->urlPrefix(), $path])), '/');
    }

    /**
     * Generate the route name
     * Generates the route name to be used in Route::methods
     * i.e. Route::get(some_generated_path_here, ['as'=>generated_route_name_here])
     * @param $name
     * @return mixed
     */
    protected function getRouteName($name)
    {
        return trim(str_replace('..', '.', join('.', [config('modules.route_name_prefix'), $this->routePrefix(), $name])), '.');
    }

    /**
     * Generate the URL for the route
     * @param $routeName
     * @param array $params
     * @param bool $absolute
     * @param Route|null $route
     * @return Route
     */
    protected function getPathForRoute($routeName, array $params = [], $absolute = true, Route $route = null)
    {
        return route($this->getRouteName($routeName), $params, $absolute, $route);
    }

    public function registerViewsPath($app = null)
    {
        if (!$this->viewsPath()) {
            return;
        }
        $app = $app ?: app();
        if (is_dir($appPath = $app->basePath() . '/resources/views/vendor/' . $this->id())) {
            $app['view']->addNamespace($this->id(), $appPath);
        }

        $app['view']->addNamespace($this->id(), $this->viewsPath());
    }

    public function registerRoutes($app = null)
    {
        if (!$this->routesPath()) {
            return;
        }
        $app = $app ?: app();
        if ($app->routesAreCached()) {
            Artisan::call('cache:clear');
        }
        require_once $this->routesPath();
    }

    public function registerFrameworkResources($app = null)
    {
        if ($this->isDisabled()) {
            return;
        }
        $app = $app ?: app();
        $this->registerViewsPath($app);
        $this->registerRoutes($app);
    }

}