<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-20
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class ChairmanController extends ControllerIndex
{
    public $arrReturn = [];//返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        //会长信息
        $chairman = Db::name('chairman')->paginate(20);

        $this->arrReturn = parent::getReturnArr(3, 0, 1);
        $this->arrReturn['chairman'] = $chairman;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }
}