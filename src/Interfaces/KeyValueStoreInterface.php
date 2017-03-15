<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/14/17
 * Time: 6:43 PM
 */

namespace ItvisionSy\Laravel\Modules\Interfaces;

interface KeyValueStoreInterface
{

    public function set($key, $value = null);

    public function get($key, $default = null);

}