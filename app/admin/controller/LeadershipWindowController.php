<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Loader;
use think\Config;
use think\Db;
use think\cache\driver\Redis;

class LeadershipWindowController extends ControllerBase
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
        $result = $redis->get('VAE_LEADERSHIP_WINDOW');
        if ($result && empty($param['keywords'])) {
            return vae_assign_table(0, '', $result);
        }
        if (!empty($param['keywords'])) {
            $where['id|name|job'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('LeadershipWindow')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        if (empty($param['keywords'])) {
            $redis->set('VAE_LEADERSHIP_WINDOW', $content, 86400*7);
        }
        return vae_assign_table(0,'',$content);
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
        if ($this->request->isPost()) {
            $param = vae_get_param();
            $param['created_at'] = date('Y-m-d H:i:s');
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\LeadershipWindow.add');
            if ($result !== true) {
                return vae_assign(0, $result);
            } else {
                Loader::model('LeadershipWindow')->strict(false)->field(true)->insert($param);
                $redis = new Redis();
                $redis->rm('VAE_LEADERSHIP_WINDOW');
                return vae_assign();
            }
        }
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $result = Db::name('LeadershipWindow')->where(['id' => $id])->find();
        return view('', ['content' => $result]);
    }

    /**
     * 提交修改
     */
    public function editSubmit()
    {
        if ($this->request->isPost()) {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\LeadershipWindow.edit');
            if ($result !== true) {
                return vae_assign(0, $result);
            } else {
                Loader::model('LeadershipWindow')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_LEADERSHIP_WINDOW');
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
        $flag = Loader::model('LeadershipWindow')->where(['id' => $id])->delete();
        if ($flag) {
            $redis = new Redis();
            $redis->rm('VAE_LEADERSHIP_WINDOW');
            return vae_assign(1, '删除成功');
        } else {
            return vae_assign(0, '删除失败');
        }
    }
}