<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-21
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class JoinUsController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $join_us = Db::name('join_us')->select();

        $this->arrReturn = parent::getReturnArr(3, 0, 4);
        $this->arrReturn['join_us'] = $join_us;

        return view('', $this->arrReturn);
    }
}