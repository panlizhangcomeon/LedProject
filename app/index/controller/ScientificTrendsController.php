<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class ScientificTrendsController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $scientific_trends = Db::name('scientific_trends')->order('publish_day', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(4, 1, 1);
        $this->arrReturn['scientific_trends'] = $scientific_trends;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    public function show()
    {
        $id = vae_get_param('id');

        $data = Db::name('scientific_trends')->find(['id' => $id]);

        $tid = $data['tid'];

        $read = parent::addRead($tid); //增加阅读量

        parent::addHistory($data['title'], $id, $data['publish_day'], 'scientific_trends'); //增加历史纪录

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('scientific_trends')->field('id')->order('publish_day', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'scientific_trends');

        $this->arrReturn = parent::getReturnArr(4, 1, 1);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];
        $this->arrReturn['order_news'] = parent::getOrderNewsList();
        $this->arrReturn['read'] = empty($read) ? 0 : $read;

        return view('', $this->arrReturn);
    }
}