<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 4:30
 */
namespace app\index\controller;

use think\Db;
use think\Session;
use vae\controller\ControllerIndex;

class HistoryController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $username = Session::get('username');
        $flag = vae_get_param('flag');
        $data = Db::name('history')->where(['username' => $username])->order('updated_at', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(6, 3, 1);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }
}