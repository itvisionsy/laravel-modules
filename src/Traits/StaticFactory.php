<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/16/17
 * Time: 10:53 AM
 */

namespace ItvisionSy\Laravel\Modules\Traits;


trait StaticFactory
{

    /**
     * @return static|$this
     */
    public static function make(){
        return new static();
    }

}