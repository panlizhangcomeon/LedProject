<?php
// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +---------------------------------------------------------------------
namespace app\admin\controller;
use vae\controller\AdminCheckAuth;
use think\Db;
use vae\controller\ControllerBase;

class HookController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    //列表
    public function getHookList()
    {
    	$param = vae_get_param();
        $where = array();
        if(!empty($param['keywords'])) {
            $where['id|name|desc'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? \think\Config::get('paginate.list_rows') : $param['limit'];
        $hook = Db::name('hook')->where($where)->order('id asc')->paginate($rows,false,['query'=>$param]);
    	return vae_assign_table(0,'',$hook);
    }

    // 获得网站访问数据
    public function getVisitData()
    {
        $data = getPvUvIp();
        return vae_assign(0, '', $data);
    }
}
