<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class PatentController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $policy_broadcast = Db::name('patent')->paginate(20);

        $this->arrReturn = parent::getReturnArr(4, 1, 4);
        $this->arrReturn['patent'] = $policy_broadcast;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }
}