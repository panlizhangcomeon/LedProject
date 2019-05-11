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

class GeneralProfileController extends ControllerBase
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
        $result = $redis->get('VAE_GENERAL_PROFILE');
        if ($result) {
            return vae_assign_table(0, '', $result);
        }
        $param = vae_get_param();
        $where = [];
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('GeneralProfile')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows, false, ['query' => $param]);
        $redis->set('VAE_GENERAL_PROFILE', $content, 86400*7);
        return vae_assign_table(0, '', $content);
    }

    public function edit()
    {
        $id = vae_get_param('id');
        $content = Db::name('general_profile')->where(['id' => $id])->find();
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
            $result = $this->validate($param, 'app\admin\validate\GeneralProfile.edit');
            if ($result === true) {
                Loader::model('GeneralProfile')->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_GENERAL_PROFILE');
                return vae_assign();
            } else {
                return vae_assign(0, $result);
            }
        }
    }
}