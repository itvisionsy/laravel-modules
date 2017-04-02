<?php

namespace ItvisionSy\Laravel\Modules\Tests;

/**
 * Description of ExtendedModules
 *
 * @author muhannad
 */
class ExtendedModules extends \ItvisionSy\Laravel\Modules\Modules {

    public static function resetStoreHandler() {
        static::$storeHandler = null;
    }

}
