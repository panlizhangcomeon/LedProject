<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Db;
use think\Config;
use think\Loader;
use think\cache\driver\Redis;

class OrganizationStructureController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    //列表
    public function getContentList()
    {
        $redis = new Redis();
        $result = $redis->get('VAE_ORGANIZATION_STRUCTURE');
        if ($result) {
            return vae_assign_table(0, '', $result);
        }
        $param = vae_get_param();
        $where = array();
        $rows = empty($param['limit']) ? Config::get('paginate.list_rows') : $param['limit'];
        $content = Loader::model('OrganizationStructure')
            ->field('*')
            ->order('updated_at desc')
            ->where($where)
            ->paginate($rows,false,['query'=>$param]);
        $redis->set('VAE_ORGANIZATION_STRUCTURE', $content, 86400*7);
        return vae_assign_table(0,'',$content);
    }

    //修改
    public function edit()
    {
        $id = vae_get_param('id');
        $result = Db::name('organization_structure')->where(['id' => $id])->find();
        return view('', ['result' => $result]);
    }

    //提交修改
    public function editSubmit()
    {
        if($this->request->isPost())
        {
            $param = vae_get_param();
            $param['updated_at'] = date('Y-m-d H:i:s');
            $validate = $this->validate($param, 'app\admin\validate\OrganizationStructure.edit');
            if ($validate !== true) {
                return vae_assign(0, $validate);
            } else {
                Loader::model('OrganizationStructure')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                $redis = new Redis();
                $redis->rm('VAE_ORGANIZATION_STRUCTURE');
                return vae_assign();
            }
        }
    }
}