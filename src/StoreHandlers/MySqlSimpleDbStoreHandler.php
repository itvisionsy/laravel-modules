<?php

namespace ItvisionSy\Laravel\Modules\StoreHandlers;

/**
 * Description of MySqlSimpleDbStoreHandler
 *
 * @author muhannad
 */
class MySqlSimpleDbStoreHandler extends SimpleDbStoreHandler {

    public function set($key, $value = null) {
        return $this->statement("REPLACE INTO `" . static::$tableName . "` (`key`,`value`) VALUES ('" . addslashes($key) . "','" . addslashes(serialize($value)) . "')");
    }

}
