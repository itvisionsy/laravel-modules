<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 2:58 AM
 */

namespace ItvisionSy\Laravel\Modules\StoreHandlers;

use DB;
use ItvisionSy\Laravel\Modules\Interfaces\KeyValueStoreInterface;

class SimpleDbStoreHandler implements KeyValueStoreInterface
{

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

    public function set($key, $value = null)
    {
        return $this->statement("INSERT OR REPLACE INTO `" . static::$tableName . "` (`key`,`value`) VALUES ('" . addslashes($key) . "','" . addslashes($value) . "')");
    }

    public function get($key, $default = null)
    {
        $result = $this->select("SELECT `value` FROM `" . static::$tableName . "` WHERE `key`='" . addslashes($key) . "'");
        if (count($result)) {
            return $result[0]->value;
        }
        return $default;
    }
}