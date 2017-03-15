<?php

/**
 * Created by PhpStorm.
 * User: mhh14
 * Date: 9/8/2016
 * Time: 8:59 AM
 */

namespace ItvisionSy\Laravel\Modules\Traits;

trait StaticAndInstanceAccessTrait
{

    public function __call($method, $vars)
    {
        if (array_search($method, $this->grantAccess()) !== false) {
            return call_user_func_array([$this, $method], $vars);
        }
    }

    public static function __callStatic($method, $vars)
    {
        if (array_search($method, static::grantAccess()) !== false) {
            return call_user_func_array([new static(), $method], $vars);
        }
    }

}