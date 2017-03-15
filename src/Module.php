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

abstract class Module implements StaticAndInstanceAccessInterface
{

    use StaticAndInstanceAccessTrait;

    static protected $_routes = [];

    protected $moduleId;
    protected $moduleName;
    protected $moduleRouteNamePrefix;
    protected $moduleUrlPrefix;

    public static function grantAccess()
    {
        return ['id', 'name', 'isEnabled', 'isDisabled', 'isSystem', 'isNormal', 'routesPath'];
    }

    /**
     * @return Module|static|$this
     */
    public static function make()
    {
        return new static();
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
    public function id()
    {
        return $this->moduleId;
    }

    /**
     * @return string display name of the module
     */
    public function name()
    {
        return $this->moduleName;
    }

    /**
     * @return mixed
     */
    public function routePrefix()
    {
        return $this->moduleRouteNamePrefix ?: $this->id();
    }

    /**
     * @return mixed
     */
    public function urlPrefix()
    {
        return $this->moduleUrlPrefix ?: $this->id();
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return Modules::isModuleEnabled($this);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * @return boolean
     */
    public function isSystemModule()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isNormalModule()
    {
        return !$this->isSystemModule();
    }

    /**
     * @return mixed
     */
    public function enableModule()
    {
        return Modules::enableModule($this);
    }

    /**
     * @return mixed
     */
    public function disableModule()
    {
        return Modules::disableModule($this);
    }

    /**
     * Path relative to module root
     * @param $path
     * @return string
     */
    public function modulePath($path)
    {
        $class = explode("\\", get_class($this))[2];
        $fullPath = rtrim(config('modules.directory'), "\\") . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . ($path ? $path : "");
        return $fullPath;
    }

    /**
     * @return null|string path to views resources.
     */
    public function viewsPath()
    {
        $dir = $this->modulePath("Views" . DIRECTORY_SEPARATOR);
        if (is_dir($dir)) {
            return $dir;
        }
        $dir = $this->modulePath("views" . DIRECTORY_SEPARATOR);
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
    public function getViewName($name)
    {
        return $this->id() . "::" . $name;
    }

    /**
     * @param $viewName abstract view name. Key will be automatically added
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderView($viewName, array $data = [], array $mergeData = [])
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
    public function getRoutePath($path)
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
    public function getRouteName($name)
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
    public function getPathForRoute($routeName, array $params = [], $absolute = true, Route $route = null)
    {
        return route($this->getRouteName($routeName), $params, $absolute, $route);
    }

}