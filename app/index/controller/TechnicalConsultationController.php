<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-21
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class TechnicalConsultationController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $join_us = Db::name('technical_consultation')->select();

        $this->arrReturn = parent::getReturnArr(7, 4, 1);
        $this->arrReturn['technical_consultation'] = $join_us;

        return view('', $this->arrReturn);
    }
}