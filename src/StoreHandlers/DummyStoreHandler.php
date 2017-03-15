<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 2:58 AM
 */

namespace ItvisionSy\Laravel\Modules\StoreHandlers;

use ItvisionSy\Laravel\Modules\Interfaces\KeyValueStoreInterface;

class DummyStoreHandler implements KeyValueStoreInterface
{

    public function set($key, $value = null)
    {
        return $value;
    }

    public function get($key, $default = null)
    {
        return $default;
    }
}