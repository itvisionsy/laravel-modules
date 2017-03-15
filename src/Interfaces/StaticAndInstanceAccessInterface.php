<?php
/**
 * Created by PhpStorm.
 * User: mhh14
 * Date: 9/8/2016
 * Time: 8:58 AM
 */

namespace ItvisionSy\Laravel\Modules\Interfaces;


interface StaticAndInstanceAccessInterface
{

    public function __call($method, $vars);

    public static function __callStatic($method, $vars);

    public static function grantAccess();

}