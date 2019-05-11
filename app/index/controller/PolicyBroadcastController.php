<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class PolicyBroadcastController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $policy_broadcast = Db::name('policy_broadcast')->order('publish_date', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(6, 3, 3);
        $this->arrReturn['policy_broadcast'] = $policy_broadcast;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    public function show()
    {
        $id = vae_get_param('id');

        $data = Db::name('policy_broadcast')->find(['id' => $id]);

        $tid = $data['tid'];

        $read = parent::addRead($tid); //增加阅读量

        parent::addHistory($data['title'], $id, $data['publish_date'], 'policy_broadcast'); //增加历史纪录

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('policy_broadcast')->field('id')->order('publish_date', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'policy_broadcast');

        $this->arrReturn = parent::getReturnArr(6, 3, 3);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];
        $this->arrReturn['order_news'] = parent::getOrderNewsList();
        $this->arrReturn['read'] = empty($read) ? 0 : $read;

        return view('', $this->arrReturn);
    }
}