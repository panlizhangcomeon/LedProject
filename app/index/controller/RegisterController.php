<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 23:50
 */
namespace app\index\controller;

use think\Db;
use think\Session;
use vae\controller\ControllerIndex;

class RegisterController extends ControllerIndex
{
    public function index()
    {
        return view();
    }

    public function checkEmail()
    {
        $username = vae_get_param('username');
        $password = md5(vae_get_param('password'));
        return view('', [
            'username' => $username,
            'password' => $password
        ]);
    }

    public function send()
    {
        $res = ['code' => 1, 'msg' => '邮件发送成功'];
        $yzm = '';
        for ($i = 0; $i<4; $i++) {
            $yzm.=rand(0,9);
        }
        $email = vae_get_param('email');
        $reg = sendEmail([['user_email'=>$email,'content'=>'您的注册验证码为 ' . $yzm . '，24小时内有效']]);

        //发送成功就删除
        if ($reg) {
            Session::set('email_yzm', $yzm);
        } else {
            $res['code'] = 0;
            $res['msg'] = '邮件发送失败';
        }
        return json_encode($res);
    }

    public function register()
    {
        $data = vae_get_param();
        $yzm = $data['yzm'];
        unset($data['yzm']);
        if ($yzm != Session::get('email_yzm')) {
            $this->error('验证码输入错误');
        } else {
            $reg = Db::name('user')->where(['username' => $data['username']])->count();
            if ($reg) {
                $this->error('用户名已存在');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                $t = Db::name('user')->insert($data);
                if ($t) {
                    $this->success('注册成功', '/index/login/index');
                } else {
                    $this->error('注册失败，请联系网站管理员');
                }
            }
        }
    }
}