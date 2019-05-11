<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-18
 */
namespace app\index\controller;
use vae\controller\ControllerIndex;
use think\Db;

class GeneralProfileController extends ControllerIndex
{
    public $arrReturn = [];//返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        //研究中心
        $association = Db::name('general_profile')->find();

        $this->arrReturn = parent::getReturnArr(5, 2, 0);
        $this->arrReturn['general_profile'] = $association;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }
}