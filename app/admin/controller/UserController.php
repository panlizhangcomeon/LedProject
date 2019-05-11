<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 3:57
 */
namespace app\admin\controller;

use think\Db;
use vae\controller\ControllerBase;
use think\Loader;
use think\Config;

class UserController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    //列表
    public function getContentList()
    {
        $param = vae_get_param();
        $where = array();
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('User')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows,false,['query'=>$param]);
        return vae_assign_table(0,'',$content);
    }

    //改变账号状态
    public function changeStatus()
    {
        $status = (int)vae_get_param('status');
        $id = (int)vae_get_param('id');
        $message = ['code' => 1, 'msg' => '更新成功'];
        $reg = Db::name('user')->update(['id' => $id, 'status' => $status]);
        if (!$reg) {
            $message['code'] = 0;
            $message['msg'] = '更新失败';
        }
        return json_encode($message);
    }

    public function audit()
    {
        return view();
    }

    public function getAuditList()
    {
        $param = vae_get_param();
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $where = ['status' => 0];
        $content = Loader::model('Audit')
            ->field('id, username, pay_img, created_at, updated_at')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows,false,['query'=>$param]);
        return vae_assign_table(0,'',$content);
    }

    //审核会员
    public function vipStatus()
    {
        $status = (int)vae_get_param('status');
        $id = (int)vae_get_param('id');
        $message = ['code' => 1, 'msg' => '审核成功'];
        $reg = Db::name('audit')->update(['id' => $id, 'status' => $status]);
        if (!$reg) {
            $message['code'] = 0;
            $message['msg'] = '更新审核记录表失败';
        }
        if ($status == 1) {
            $data = Db::name('audit')->where(['id' => $id])->find();
            $username = $data['username'];
            $t = Db::name('user')->where(['username' => $username])->update(['is_vip' => 1]);
            if (!$t) {
                $message['code'] = 0;
                $message['msg'] = '更新会员记录表失败';
            }
        }
        return json_encode($message);
    }
}