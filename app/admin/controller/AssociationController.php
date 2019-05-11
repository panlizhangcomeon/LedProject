<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Loader;
use think\Cache;
use think\Db;
use think\Config;
use think\cache\driver\Redis;

class AssociationController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    //列表
    public function getContentList()
    {
        $redis = new Redis();
        $param = vae_get_param();
        $where = array();
        $result = $redis->get('VAE_ASSOCIATION');
        if ($result) {
            return vae_assign_table(0, '', $result);
        }
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('Association')
            ->field('id,title,content,updated_at')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows,false,['query'=>$param]);
        $redis->set('VAE_ASSOCIATION', $content, 86400*7);
        return vae_assign_table(0,'',$content);
    }

    //修改
    public function edit()
    {
        $id = vae_get_param('id');
        $association = Db::name('association')->where(['id'=>$id])->find();
        return view('', ['association' => $association]);
    }

    //提交修改
    public function editSubmit()
    {
        if($this->request->isPost()){
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->validate($param, 'app\admin\validate\Association.edit');
            if ($result !== true) {
                return vae_assign(0,$result);
            } else {
                Loader::model('Association')->where(['id'=>$param['id']])->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_ASSOCIATION');
                return vae_assign();
            }
        }
    }
}