<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 2:58 AM
 */

namespace ItvisionSy\Laravel\Modules\StoreHandlers;

use DB;
use Illuminate\Database\Connection;
use ItvisionSy\Laravel\Modules\Interfaces\KeyValueStoreInterface;
use ItvisionSy\Laravel\Modules\Traits\StaticFactory;

abstract class SimpleDbStoreHandler implements KeyValueStoreInterface
{

    use StaticFactory;

    protected static $tableName = 'modules_storage';

    /**
     * @return Connection
     */
    public static function getConnection()
    {
        $connectionDriver = config('modules.default_store_handler_connection', null) ?: config('database.default');
        /** @var Connection $connection */
        $connection = DB::connection($connectionDriver);
        return $connection;
    }

    /**
     * @return mixed
     */
    public static function createTable()
    {
        return static::statement("CREATE TABLE IF NOT EXISTS `" . static::$tableName . "` (`key` VARCHAR(200) UNIQUE NOT NULL PRIMARY KEY, `value` VARCHAR(200) NULL);");
    }

    protected static function statement($query, array $bindings = [])
    {
        return static::getConnection()->statement($query, $bindings);
    }

    protected static function select($query, array $bindings = [])
    {
        return static::getConnection()->select($query, $bindings);
    }

    abstract public function set($key, $value = null);

    public function get($key, $default = null)
    {
        $result = $this->select("SELECT `value` FROM `" . static::$tableName . "` WHERE `key`='" . addslashes($key) . "'");
        if (count($result)) {
            return unserialize($result[0]->value);
        }
        return $default;
    }
}