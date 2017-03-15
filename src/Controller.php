<?php
/**
 * Created by PhpStorm.
 * User: Muhannad Shelleh <muhannad.shelleh@live.com>
 * Date: 3/15/17
 * Time: 5:15 AM
 */

namespace ItvisionSy\Laravel\Modules;

use App\Http\Controllers\Controller as BaseController;
use ErrorException;

abstract class Controller extends BaseController
{

    /** @var Module */
    protected $module;

    /**
     * @param $viewName
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderView($viewName, array $data = [], array $mergeData = [])
    {
        return $this->module()->renderView($viewName, $data, $mergeData);
    }

    /**
     * @return Module
     * @throws ErrorException
     */
    public function module()
    {
        if (!$this->module) {
            $ns = trim(get_class($this), "\\");
            $baseNs = trim(config('modules.namespace'), "\\");
            if (!starts_with($ns, $baseNs)) {
                throw new ErrorException('Module controllers should exist inside module root folder');
            }
            $moduleId = explode("\\", trim(substr($ns, strlen($baseNs)), "\\"))[0];
            $moduleFullName = join("\\", ["", $baseNs, $moduleId, config('modules.class_name')]);
            if (!class_exists($moduleFullName)) {
                throw new ErrorException("Module class does not exist");
            }
            $this->module = $moduleFullName::make();
        }
        return $this->module;
    }

}