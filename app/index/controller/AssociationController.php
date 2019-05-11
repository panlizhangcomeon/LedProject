<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-18
 */
namespace app\index\controller;
use vae\controller\ControllerIndex;
use think\Db;

class AssociationController extends ControllerIndex
{
    public $arrReturn = [];//返回渲染视图的数组

    public function index()
    {
        //协会简介
        $association = Db::name('association')->find();

        $this->arrReturn = parent::getReturnArr(3, 0, 0);
        $this->arrReturn['association'] = $association;

        return view('', $this->arrReturn);
    }
}