<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-07
 */
namespace app\admin\controller;

use think\Loader;
use think\Db;
use think\Request;
use think\Config;
use vae\controller\ControllerBase;
use think\cache\driver\Redis;

class TechnicalConsultationController extends ControllerBase
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
        $result = $redis->get('VAE_TECHNICAL_CONSULTATION');
        if ($result && empty($param['keywords'])) {
            return vae_assign_table(0, '', $result);
        }
        if (!empty($param['keywords'])) {
            $where['id|title|content|tel'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('TechnicalConsultation')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        if (empty($param['keywords'])) {
            $redis->set('VAE_TECHNICAL_CONSULTATION', $content, 86400*7);
        }
        return vae_assign_table(0, '', $content);
    }

    public function add()
    {
        return view('');
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
            $result = $this->validate($param, 'app\admin\validate\TechnicalConsultation.add');
            if ($result === true) {
                Loader::model('TechnicalConsultation')->strict(false)->field(true)->insert($param);
                $redis = new Redis();
                $redis->rm('VAE_TECHNICAL_CONSULTATION');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $content = Db::name('technical_consultation')->where(['id' => $id])->find();
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
            $result = $this->validate($param, 'app\admin\validate\TechnicalConsultation.edit');
            if ($result === true) {
                Loader::model('TechnicalConsultation')->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_TECHNICAL_CONSULTATION');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function delete()
    {
        $id = vae_get_param('id');
        $flag = Loader::model('TechnicalConsultation')->where(['id' => $id])->delete();
        if ($flag) {
            $redis = new Redis();
            $redis->rm('VAE_TECHNICAL_CONSULTATION');
            return vae_assign(1, '删除成功');
        } else {
            return vae_assign(0, '删除失败');
        }
    }

}