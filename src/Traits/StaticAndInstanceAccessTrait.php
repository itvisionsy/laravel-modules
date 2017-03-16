<?php

/**
 * Created by PhpStorm.
 * User: mhh14
 * Date: 9/8/2016
 * Time: 8:59 AM
 */

namespace ItvisionSy\Laravel\Modules\Traits;

use Psy\Exception\ErrorException;

trait StaticAndInstanceAccessTrait
{

    public function __call($method, $vars)
    {
        if (array_search($method, $this->grantAccess()) !== false) {
            return call_user_func_array([$this, $method], $vars);
        }
        throw new ErrorException("Method can not be called in public context");
    }

    public static function __callStatic($method, $vars)
    {
        if (array_search($method, static::grantAccess()) !== false) {
            return call_user_func_array([new static(), $method], $vars);
        }
        throw new ErrorException("Method can not be called in static context");
    }

}