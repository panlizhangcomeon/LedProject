<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-18
 */
namespace app\admin\controller;

use think\Config;
use think\Loader;
use think\Request;
use vae\controller\ControllerBase;
use think\Db;
use think\cache\driver\Redis;

class JoinUsController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    /**
     * 获取列表内容
     */
    public function getContentList()
    {
        $redis = new Redis();
        $param = vae_get_param();
        $where = [];
        $result = $redis->get('VAE_JOIN_US');
        if ($result && empty($param['keywords'])) {
            return vae_assign_table(0, '', $result);
        }
        if (!empty($param['keywords'])) {
            $where['id|department|tel|desc|locate|zip_code'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('JoinUs')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        if (empty($param['keywords'])) {
            $redis->set('VAE_JOIN_US', $content, 86400*7);
        }
        return vae_assign_table(0, '', $content);
    }

    public function add()
    {
        return view();
    }

    /**
     * 提交添加
     */
    public function addSubmit()
    {
        if (Request::instance()->isPost()) {
            $param = vae_get_param();
            $param['created_at'] = date('Y-m-d H:i:s');
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\JoinUs.add');
            if ($result !== true) {
                return vae_assign(0, $result);
            } else {
                Loader::model('JoinUs')->strict(false)->field(true)->insert($param);
                $redis = new Redis();
                $redis->rm('VAE_JOIN_US');
                return vae_assign();
            }
        }
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $content = Db::name('join_us')->where(['id' => $id])->find();
        return view('', ['content' => $content]);
    }

    /**
     * 提交修改
     */
    public function editSubmit()
    {
        if (Request::instance()->isPost()) {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\JoinUs.edit');
            if ($result !== true) {
                return vae_assign(0, $result);
            } else {
                Loader::model('JoinUs')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_JOIN_US');
                return vae_assign();
            }
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = vae_get_param('id');
        $flag = Loader::model('JoinUs')->where(['id' => $id])->delete();
        if ($flag) {
            $redis = new Redis();
            $redis->rm('VAE_JOIN_US');
            return vae_assign(1, '删除成功');
        } else {
            return vae_assign(0, '删除失败');
        }
    }
}