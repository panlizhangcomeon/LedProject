<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Config;
use think\Loader;
use think\Request;
use think\Db;
use think\cache\driver\Redis;

class AssociationAlbumController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    /**
     * 获得列表内容接口
     */
    public function getContentList()
    {
        $redis = new Redis();
        $param = vae_get_param();
        $where = [];
        $result = $redis->get('VAE_ASSOCIATION_ALBUM');
        if ($result && empty($param['keywords'])) {
            return vae_assign_table(0, '', $result);
        }
        if (!empty($param['keywords'])) {
            $where['id|author|title'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('AssociationAlbum')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        if (empty($param['keywords'])) {
            $redis->set('VAE_ASSOCIATION_ALBUM', $content, 86400*7);
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
            $result = $this->validate($param, 'app\admin\validate\AssociationAlbum.add');
            if ($result === true) {
                Loader::model('AssociationAlbum')->strict(false)->field(true)->insert($param);
                $redis = new Redis();
                $redis->rm('VAE_ASSOCIATION_ALBUM');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $content = Db::name('association_album')->where(['id' => $id])->find();
        return view('', ['content' => $content]);
    }

    /**
     * 编辑提交
     */
    public function editSubmit()
    {
        if (Request::instance()->isPost()) {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\AssociationAlbum.edit');
            if ($result === true) {
                Loader::model('AssociationAlbum')->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_ASSOCIATION_ALBUM');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function delete()
    {
        $id = vae_get_param('id');
        $flag = Loader::model('AssociationAlbum')->where(['id' => $id])->delete();
        if ($flag) {
            $redis = new Redis();
            $redis->rm('VAE_ASSOCIATION_ALBUM');
            return vae_assign(1, '删除成功');
        } else {
            return vae_assign(0, '删除失败');
        }
    }
}