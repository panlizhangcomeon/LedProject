<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/11
 * Time: 15:51
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Session;
use think\Db;

class LikeController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $username = Session::get('username');
        $flag = vae_get_param('flag');
        $data = Db::name('history')->where(['username' => $username, 'is_like' => 1])->paginate(20);

        $this->arrReturn = parent::getReturnArr(6, 3, 1);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    public function changeStatus()
    {
        $status = (int)vae_get_param('status');
        $message = ['code' => 0, 'msg' => '成功', 'status' => $status];
        $history_id = (int)vae_get_param('history_id');
        $behavior = $status ? '收藏' : '取消收藏';
        $reg = Db::name('history')->update(['id' => $history_id, 'is_like' => $status]);
        if ($reg) {
            $message['code'] = 1;
            $message['msg'] = $behavior . '成功';
        } else {
            $message['msg'] = $behavior . '失败';
        }
        echo json_encode($message);
    }
}