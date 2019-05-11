<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-04-26
 */
namespace app\index\controller;

use think\Db;
use think\Session;
use vae\controller\ControllerIndex;
use think\Model;

class UserController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    //个人中心首页
    public function index()
    {
        $username = Session::get('username');
        $data = Db::name('user')->where(['username' => $username])->find();

        $this->arrReturn = parent::getReturnArr(6, 3, 2);
        $this->arrReturn['data'] = $data;
        return view('', $this->arrReturn);
    }

    //付款页面
    public function pay()
    {
        $this->arrReturn = parent::getReturnArr(6, 3, 2);
        return view('', $this->arrReturn);
    }

    //上传文件
    public function upload()
    {
        //获取表单上传文件
        $file = request()->file('image');
        if (empty($file)) {
            $this->error('您未上传任何图片');
        }
        $info = $file->validate(['size' => 10000000, 'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'pay');
        if ($info) {
            $save_name = $info->getSaveName();
            $path = '/upload/pay/' . $save_name;
            $username = Session::get('username');
            $data = Db::name('audit')->where(['username' => $username])->find();
            if (empty($data)) {
                $reg = Db::name('audit')->insert([
                    'username' => $username,
                    'pay_img' => $path,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if ($reg) {
                    $this->success('上传截图成功，等待管理员审核', '/index/user/index?flag=personal');
                } else {
                    $this->error('数据插入失败,请联系网站管理员');
                }
            } else {
                $reg = Db::name('audit')->where(['username' => $username])->update([
                    'pay_img' => $path,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status' => 0
                ]);
                if ($reg) {
                    $this->success('更新截图成功，等待管理员审核', '/index/user/index?flag=personal');
                } else {
                    $this->error('数据更新失败,请联系网站管理员');
                }
            }
        } else {
            $msg = $file->getError();
            $this->error($msg);
        }
    }

    //修改个人信息
    public function changeInfo()
    {
        $data = vae_get_param();
        $username = Session::get('username');
        $reg = Db::name('user')->where(['username' => $username])->update([
            'password' => md5($data['password']),
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if ($reg) {
            $this->success('个人信息修改成功', '/index/user/index?flag=personal');
        } else {
            $this->error('修改失败，请联系网站管理员');
        }
    }
}