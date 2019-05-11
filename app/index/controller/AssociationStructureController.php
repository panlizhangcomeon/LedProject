<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-19
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class AssociationStructureController extends ControllerIndex
{
    public $arrReturn;//返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        //协会架构图
        $association_structure = Db::name('association_structure')->find();


        $this->arrReturn = parent::getReturnArr(3, 0, 2);
        $this->arrReturn['association_structure'] = $association_structure;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }
}