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
use think\Request;
use think\Loader;
use think\Cache;
use think\Config;

class NavController extends AdminCheckAuth
{
    public function index()
    {
        return view();
    }

    //列表
    public function getNavList()
    {
    	$param = vae_get_param();
        $where = array();
        $data = Cache::get('VAE_NAV');
        if (empty($param['keywords']) && $data) {
            return vae_assign_table(0, '', $data);
        }
        if(!empty($param['keywords'])) {
            $where['id|name|title|desc'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $nav = Loader::model('Nav')
    			->order('create_time asc')
                ->where($where)
    			->paginate($rows,false,['query'=>$param]);
        Cache::set('VAE_NAV', $nav, 86400);
    	return vae_assign_table(0,'',$nav);
    }

    //添加
    public function add()
    {
    	return view();
    }

    //提交添加
    public function addSubmit()
    {
    	if($this->request->isPost()){
    		$param = vae_get_param();
    		$result = $this->validate($param, 'app\admin\validate\Nav.add');
            if ($result !== true) {
                return vae_assign(0,$result);
            } else {
				Loader::model('Nav')->strict(false)->field(true)->insert($param);
				Cache::rm('VAE_NAV');
                return vae_assign();
            }
    	}
    }

    //修改
    public function edit()
    {
        $id    = vae_get_param('id');
        $nav = Db::name('nav')->find($id);
        return view('',[
            'nav'=>$nav
        ]);
    }

    //提交修改
    public function editSubmit()
    {
        if($this->request->isPost()){
            $param = vae_get_param();
            $result = $this->validate($param, 'app\admin\validate\Nav.edit');
            if ($result !== true) {
                return vae_assign(0,$result);
            } else {
                Loader::model('Nav')->where([
                    'id'=>$param['id']
                ])->strict(false)->field(true)->update($param);
                Cache::rm('VAE_NAV');
                return vae_assign();
            }
        }
    }

    //删除
    public function delete()
    {
        $id    = vae_get_param("id");
        $count = Db::name('NavInfo')->where([
            'nav_id' => $id
        ])->count();
        if($count > 0) {
            return vae_assign(0,'该组下还有导航，无法删除');
        }
        if (Db::name('Nav')->delete($id) !== false) {
            Cache::rm('VAE_NAV');
            return vae_assign(1,"删除成功！");
        } else {
            return vae_assign(0,"删除失败！");
        }
    }

    //管理导航
    public function navInfo()
    {
        $id = vae_get_param('id');
        return view('',[
            'nav_id' => $id
        ]);
    }

    //导航列表
    public function getNavInfoList()
    {
        $id            = vae_get_param('id');
        $data = Cache::get('VAE_NAV_INFO');
        if ($data) {
            return vae_assign(0, '', $data);
        }
        $navInfoList = Db::name('nav_info')->where([
            'nav_id' => $id
        ])->order('order asc')->select();
        Cache::set('VAE_NAV_INFO', $navInfoList, 86400);
        return vae_assign(0, '', $navInfoList);
    }

    //添加导航
    public function addNavInfo()
    {
        return view('',[
            'nav_id' => vae_get_param('id'),
            'pid' => vae_get_param('pid')
        ]);
    }

    //保存添加
    public function addNavInfoSubmit()
    {
        if($this->request->isPost()){
            $param = vae_get_param();
            $result = $this->validate($param, 'app\admin\validate\Nav.addInfo');
            if ($result !== true) {
                return vae_assign(0,$result);
            } else {
                Loader::model('NavInfo')->strict(false)->field(true)->insert($param);
                //清除导航缓存
                Cache::rm('VAE_NAV_INFO');
                return vae_assign();
            }
        }
    }

    //保存修改
    public function editNavInfoSubmit()
    {
        if($this->request->isPost()) {
            $param = vae_get_param();
            $result = $this->validate($param, 'app\admin\validate\Nav.editInfo');
            if ($result !== true) {
                return vae_assign(0,$result);
            } else {
                $data[$param['field']] = $param['value'];
                $data['id'] = $param['id'];
                Loader::model('NavInfo')->strict(false)->field(true)->update($data);
                //清除导航缓存
                Cache::rm('VAE_NAV_INFO');
                return vae_assign();
            }
        }
    }

    //删除
    public function deleteNavInfo()
    {
        $id    = vae_get_param("id");
        if (Db::name('NavInfo')->delete($id) !== false) {
            //清除导航缓存
            Cache::rm('VAE_NAV_INFO');
            return vae_assign(1,"删除成功！");
        } else {
            return vae_assign(0,"删除失败！");
        }
    }
}
