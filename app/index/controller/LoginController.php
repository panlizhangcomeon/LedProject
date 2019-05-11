<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 23:13
 */
namespace app\index\controller;

use think\Db;
use think\Session;
use vae\controller\ControllerIndex;

class LoginController extends ControllerIndex
{
    public function index()
    {
        return view();
    }

    public function check()
    {
        $data = vae_get_param();
        $data['password'] = md5($data['password']);
        $reg = Db::name('user')->where($data)->find();
        if (!empty($reg)) {
            if ($reg['status'] == 1) {
                Session::set('username', $data['username']);
                Db::name('user')->update([
                    'id' => $reg['id'],
                    'last_login' => date('Y-m-d H:i:s')
                ]);
                $this->success('登陆成功', 'index/index/index');
            } else {
                $this->error('您的账号状态异常，请联系管理员');
            }
        } else {
            $this->error('用户名或密码错误');
        }
    }

    public function quit()
    {
        Session::delete('username');
        $this->success('退出成功', 'index/index/index');
    }
}