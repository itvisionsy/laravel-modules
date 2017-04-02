<?php

/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 2:58 AM
 */

namespace ItvisionSy\Laravel\Modules\StoreHandlers;

class SqliteSimpleDbStoreHandler extends SimpleDbStoreHandler {

    public function set($key, $value = null) {
        return $this->statement("INSERT OR REPLACE INTO `" . static::$tableName . "` (`key`,`value`) VALUES ('" . addslashes($key) . "','" . addslashes(serialize($value)) . "')");
    }

}
