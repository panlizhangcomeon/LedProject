<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use think\Loader;
use think\Db;
use think\Request;
use think\Config;
use vae\controller\ControllerBase;
use think\cache\driver\Redis;

class ScientificAchievementController extends ControllerBase
{
    public $cate = ['基础理论成果', '应用技术成果', '软科学成果'];

    public function index()
    {
        return view('', ['cate' => $this->cate]);
    }

    /**
     * 获得列表内容接口
     */
    public function getContentList()
    {
        $redis = new Redis();
        $param = vae_get_param();
        $where = [];
        $result = $redis->get('VAE_SCIENTIFIC_ACHIEVEMENT');
        if ($result && empty($param['keywords'])) {
            return vae_assign_table(0, '', $result);
        }
        if (!empty($param['keywords'])) {
            $where['id|cate|name'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('ScientificAchievement')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        if (empty($param['keywords'])) {
            $redis->set('VAE_SCIENTIFIC_ACHIEVEMENT', $content, 86400*7);
        }
        return vae_assign_table(0, '', $content);
    }

    public function add()
    {
        return view('', ['cate' => $this->cate]);
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
            $result = $this->validate($param, 'app\admin\validate\ScientificAchievement.add');
            if ($result === true) {
                $id = Db::name('scientific_achievement')->insertGetId($param);
                $tid = Db::name('search')->insertGetId(insertSearchData($param, $id, $param['day'], 'scientific_achievement'));
                Loader::model('ScientificAchievement')->strict(false)->field(true)->update([
                    'id' => $id,
                    'tid' => $tid
                ]);
                $redis = new Redis();
                $redis->rm('VAE_SCIENTIFIC_ACHIEVEMENT');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $content = Db::name('scientific_achievement')->where(['id' => $id])->find();
        return view('', ['content' => $content, 'cate' => $this->cate]);
    }

    /**
     * 编辑提交
     */
    public function editSubmit()
    {
        if (Request::instance()->isPost()) {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\ScientificAchievement.edit');
            if ($result === true) {
                Loader::model('ScientificAchievement')->strict(false)->field(true)->update($param);
                Loader::model('Search')->strict(false)->field(true)->update(updateSearchData($param, $param['tid'], $param['day']));
                $redis = new Redis();
                $redis->rm('VAE_SCIENTIFIC_ACHIEVEMENT');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }

    public function delete()
    {
        $id = vae_get_param('id');
        $flag = Loader::model('ScientificAchievement')->where(['id' => $id])->delete();
        if ($flag) {
            $tid = vae_get_param('tid');
            Loader::model('Search')->where(['id' => $tid])->delete();
            $redis = new Redis();
            $redis->rm('VAE_SCIENTIFIC_ACHIEVEMENT');
            return vae_assign(1, '删除成功');
        } else {
            return vae_assign(0, '删除失败');
        }
    }
}