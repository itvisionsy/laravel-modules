<?php

/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/14/17
 * Time: 6:14 PM
 */

namespace ItvisionSy\Laravel\Modules;

use DirectoryIterator;
use ErrorException;
use Exception;
use ItvisionSy\Laravel\Modules\Interfaces\KeyValueStoreInterface;
use ItvisionSy\Laravel\Modules\Interfaces\StaticAndInstanceAccessInterface;
use ItvisionSy\Laravel\Modules\StoreHandlers\DummyStoreHandler;
use ItvisionSy\Laravel\Modules\Traits\StaticAndInstanceAccessTrait;
use SplFileInfo;
use Nette\Reflection\AnnotationsParser;

/**
 * Class Modules
 * @package App\Modules
 */
class Modules implements StaticAndInstanceAccessInterface {

    use StaticAndInstanceAccessTrait;

    protected static $modules;
    protected static $filtered = [];
    protected static $storeHandler;

    public static function grantAccess() {
        return ['setStoredValue', 'getStoredValue'];
    }

    /**
     * @param Module $module
     * @return mixed
     */
    public static function isModuleEnabled(Module $module) {
        return (bool) @static::getStoredValue("module_is_enabled|" . $module->id(), config('modules.modules_enabled_by_default', 0));
    }

    /**
     * @param Module $module
     * @return mixed
     */
    public static function disableModule(Module $module) {
        static::setStoredValue("module_is_enabled|" . $module->id(), 0);
        if (array_key_exists($module->id(), static::$filtered['enabled'])) {
            unset(static::$filtered['enabled'][$module->id()]);
        }
        static::$filtered['disabled'][$module->id()] = $module;
    }

    /**
     * @param Module $module
     * @return mixed
     */
    public static function enableModule(Module $module) {
        static::setStoredValue("module_is_enabled|" . $module->id(), 1);
        if (array_key_exists($module->id(), static::$filtered['disabled'])) {
            unset(static::$filtered['disabled'][$module->id()]);
        }
        static::$filtered['enabled'][$module->id()] = $module;
    }

    /**
     * @param $key
     * @param null $default
     * @param Module $module
     * @return mixed
     */
    protected static function getStoredValue($key, $default = null, Module $module = null) {
        return static::getStoreHandler()->get(($module ? $module->id() : config("modules.store_public_prefix_key", "modules")) . '|' . $key, $default);
    }

    /**
     * @param $key
     * @param null $value
     * @param Module $module
     * @return mixed
     */
    protected static function setStoredValue($key, $value = null, Module $module = null) {
        return static::getStoreHandler()->set(($module ? $module->id() : config("modules.store_public_prefix_key", "modules")) . '|' . $key, $value);
    }

    /**
     * @return KeyValueStoreInterface
     * @throws ErrorException
     */
    public static function getStoreHandler() {
        if (!static::$storeHandler) {
            $handler = config('modules.store_handler');
            if (!$handler) {
                $handler = DummyStoreHandler::class;
            }
            if (is_callable($handler)) {
                $handler = $handler();
            }
            if (is_string($handler) && class_exists($handler)) {
                $handler = new $handler();
            }
            if (!is_object($handler) || !($handler instanceof KeyValueStoreInterface)) {
                throw new ErrorException("Store handler should be a callable returns KeyValueStoreInterface object or a class implements the interface");
            }
            static::$storeHandler = $handler;
        }
        return static::$storeHandler;
    }

    /**
     * @param KeyValueStoreInterface $handler
     */
    public static function setStoreHandler(KeyValueStoreInterface $handler) {
        static::$storeHandler = $handler;
    }

    /**
     * @param $key
     * @return Module
     * @throws Exception
     */
    public static function get($key) {
        $module = @static::all()[$key];
        if ($module) {
            return $module;
        }
        throw new \Exception("Module not found: {$key}");
    }

    /**
     * @return array
     */
    public static function refreshModules() {
        $modules = [];
        $filtered = [
            'enabled' => [],
            'disabled' => []
        ];
        $modulesPath = static::modulesDirectory();
        if (is_dir($modulesPath)) {
            $dir = new DirectoryIterator($modulesPath);
            foreach ($dir as $fileInfo) {
                /* @var DirectoryIterator $fileInfo */
                if (!$fileInfo->isDir() || $fileInfo->isDot()) {
                    continue;
                }
                //base module path
                $moduleBasePath = $fileInfo->getPathname();
                //module class file name
                $moduleClassFile = new SplFileInfo($moduleBasePath . DIRECTORY_SEPARATOR . static::moduleClassName() . ".php");
                if (!$moduleClassFile->isFile()) {
                    continue;
                }

                //get defined class
                $classes = AnnotationsParser::parsePhp(file_get_contents($moduleClassFile));

                //namespace of the base module path
                $moduleNamespace = rtrim(static::modulesNamespace(), "\\") . "\\" . $fileInfo->getFilename();
                //module class full name
                $moduleClassName = $moduleNamespace . "\\" . ltrim(static::moduleClassName(), "\\");

                //if file is not defining a module
                if (!array_key_exists(ltrim($moduleClassName, "\\"), $classes)) {
                    continue;
                }

                //check class extends module base class
                $module = new $moduleClassName();
                if (!$module instanceof Module) {
                    continue;
                }

                //module exists and is valid
                /** @var Module $module */
                //add the module
                $modules[$module->id()] = $module;

                //classify enabled/disabled modules
                if ($module->isEnabled()) {
                    $filtered['enabled'][$module->id()] = $module;
                } else {
                    $filtered['disabled'][$module->id()] = $module;
                }
            }
        }
        static::$modules = $modules;
        static::$filtered = $filtered;
        return $modules;
    }

    /**
     * @return Module[]|array
     */
    public static function all() {
        return static::$modules ?: static::refreshModules();
    }

    /**
     * @return Module[]|array
     */
    public static function enabled() {
        static::all();
        return @static::$filtered['enabled'] ?: [];
    }

    /**
     * @return Module[]|array
     */
    public static function disabled() {
        static::all();
        return @static::$filtered['disabled'] ?: [];
    }

    /**
     * @return string
     */
    public static function modulesDirectory() {
        return config('modules.directory', app_path('Modules'));
    }

    /**
     * @return string
     */
    public static function modulesNamespace() {
        return config('modules.namespace', '\\App\\Modules');
    }

    /**
     * @return mixed
     */
    public static function moduleClassName() {
        return config('modules.class_name', 'Module');
    }

    //@TODO:Move store handler methods to separate class ModulesStoreHandler to maintain single responsibility
}
