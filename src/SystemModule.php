<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/14/17
 * Time: 5:57 PM
 */

namespace ItvisionSy\Laravel\Modules;

abstract class SystemModule extends Module
{

    /**
     * @return bool
     */
    public function isSystemModule()
    {
        return true;
    }

}