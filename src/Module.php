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

abstract class Module implements StaticAndInstanceAccessInterface {

    use StaticAndInstanceAccessTrait;
    use StaticFactory;

    /**
     *
     * @return Module|$this|static|self
     */
    protected static function this() {
        if (!array_key_exists(get_called_class(), static::$startedUp)) {
            static::$startedUp[get_called_class()] = static::make();
        }
        return static::$startedUp[get_called_class()];
    }

    static protected $_routes = [];
    static protected $moduleId;
    static protected $moduleName;
    static protected $moduleRouteNamePrefix;
    static protected $moduleUrlPrefix;
    static protected $startedUp = [];

    public static function grantAccess() {
        return ['registerViewsPath', 'registerRoutes', 'registerFrameworkResources', 'registerMigrationsPath'];
    }

    public function __construct() {
        if (array_key_exists(get_called_class(), static::$startedUp) === false) {
            static::startup();
            static::$startedUp[get_called_class()] = $this;
        }
    }

    /**
     * @return mixed Loads helpers and dependencies
     */
    public static function startup() {

    }

    /**
     * Returns a path to the routes file, or false if no routes is required
     * @return bool|string
     */
    public static function routesPath() {
        $defaultPath = static::modulePath(join(DIRECTORY_SEPARATOR, ['', 'Http', 'routes.php']));
        return file_exists($defaultPath) ? $defaultPath : false;
    }

    /**
     * @return string unique identifier of the module
     */
    public static function id() {
        return static::$moduleId;
    }

    /**
     * @return string display name of the module
     */
    public static function name() {
        return static::$moduleName;
    }

    /**
     * @return mixed
     */
    public static function routePrefix() {
        return static::$moduleRouteNamePrefix ?: static::id();
    }

    /**
     * @return mixed
     */
    public static function urlPrefix() {
        return static::$moduleUrlPrefix ?: static::id();
    }

    /**
     * @return boolean
     */
    public static function isEnabled() {
        return Modules::isModuleEnabled(static::this());
    }

    /**
     * @return bool
     */
    public static function isDisabled() {
        return !static::isEnabled();
    }

    /**
     * @return mixed
     */
    public static function enableModule() {
        return Modules::enableModule(static::this());
    }

    /**
     * @return mixed
     */
    public static function disableModule() {
        return Modules::disableModule(static::this());
    }

    /**
     * Path relative to module root
     * @param $path
     * @return string
     */
    public static function modulePath($path) {
        $class = explode("\\", get_class(static::this()))[2];
        $fullPath = rtrim(config('modules.directory'), "\\") . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . ($path ? $path : "");
        return $fullPath;
    }

    /**
     *
     * @return string|null
     */
    public static function migrationsPath() {
        $dir = static::modulePath("Migrations" . DIRECTORY_SEPARATOR);
        if (is_dir($dir)) {
            return $dir;
        }
        return null;
    }

    /**
     * @return null|string path to views resources.
     */
    public static function viewsPath() {
        $dir = static::modulePath("Views" . DIRECTORY_SEPARATOR);
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
    public static function getViewName($name) {
        return static::id() . "::" . $name;
    }

    /**
     * @param $viewName abstract view name. Key will be automatically added
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function renderView($viewName, array $data = [], array $mergeData = []) {
        return \view(static::getViewName($viewName), $data, $mergeData + ['this_module' => static::this(), '__this_module' => static::this()]);
    }

    /**
     * Generate the route path
     * Generates the route path to be used in Route::methods
     * i.e. Route::get(some_generated_path_here)
     * @param $path
     * @return mixed
     */
    public static function getRoutePath($path) {
        return trim(str_replace('//', '/', join('/', [config('modules.route_prefix'), static::urlPrefix(), $path])), '/');
    }

    /**
     * Generate the route name
     * Generates the route name to be used in Route::methods
     * i.e. Route::get(some_generated_path_here, ['as'=>generated_route_name_here])
     * @param $name
     * @return mixed
     */
    public static function getRouteName($name) {
        return trim(str_replace('..', '.', join('.', [config('modules.route_name_prefix'), static::routePrefix(), $name])), '.');
    }

    /**
     * Generate the URL for the route
     * @param $routeName
     * @param array $params
     * @param bool $absolute
     * @param Route|null $route
     * @return Route
     */
    public static function getPathForRoute($routeName, array $params = [], $absolute = true, Route $route = null) {
        return route(static::getRouteName($routeName), $params, $absolute, $route);
    }

    protected static function registerViewsPath($app = null) {
        if (!static::viewsPath()) {
            return;
        }
        $app = $app ?: app();
        if (is_dir($appPath = $app->basePath() . '/resources/views/vendor/' . static::id())) {
            $app['view']->addNamespace(static::id(), $appPath);
        }

        $app['view']->addNamespace(static::id(), static::viewsPath());
    }

    protected static function registerRoutes($app = null) {
        if (!static::routesPath()) {
            return;
        }
        $app = $app ?: app();
        if ($app->routesAreCached()) {
            Artisan::call('cache:clear');
        }
        require_once static::routesPath();
    }

    protected static function registerFrameworkResources($app = null) {
        if (static::isDisabled()) {
            return;
        }
        $app = $app ?: app();
        static::registerViewsPath($app);
        static::registerRoutes($app);
    }

    protected static function setStoreValue($key, $value = null) {
        Modules::setStoredValue($key, $value, static::this());
    }

    protected static function getStoreValue($key, $default = null) {
        Modules::getStoredValue($key, $default, static::this());
    }

}
