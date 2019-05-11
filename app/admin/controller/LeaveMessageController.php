<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 15:55
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Db;
use think\Loader;
use think\Config;

class LeaveMessageController extends ControllerBase
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
        $content = Loader::model('LeaveMessage')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows,false,['query'=>$param]);
        return vae_assign_table(0,'',$content);
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $result = Db::name('leave_message')->where(['id' => $id])->find();
        return view('', ['result' => $result]);
    }

    /**
     * 提交修改
     */
    public function editSubmit()
    {
        if ($this->request->isPost()) {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\LeaveMessage');
            if ($result !== true) {
                return vae_assign(0, $result);
            } else {
                Loader::model('LeaveMessage')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                return vae_assign();
            }
        }
    }
}