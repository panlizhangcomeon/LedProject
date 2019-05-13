<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class ScientificAchievementController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public $cate_arr = ['基础理论成果', '应用理论成果', '软科学成果'];

    public function index()
    {
        $flag = vae_get_param('flag');

        $policy_broadcast = Db::name('scientific_achievement')->order('day', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(4, 1, 3);
        $this->arrReturn['scientific_achievement'] = $policy_broadcast;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    /**
     * 科研设备详情
     * @return \think\response\View
     */
    public function show()
    {
        $id = (int)vae_get_param('id');

        $data = Db::name('scientific_achievement')->where(['id' => $id])->find();

        $tid = $data['tid'];

        $read = parent::addRead($tid); //增加阅读量

        parent::addHistory($data['name'], $id, $data['day'], 'scientific_achievement'); //增加历史纪录

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('scientific_achievement')->field('id')->order('day', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'scientific_achievement');

        //获得用户收藏信息
        $history = parent::getHistory($data['name'], $id, 'scientific_achievement');
        if (!empty($history)) {
            $is_like = $history['is_like'];
            $history_id = $history['id'];
        }

        //获得文章评论
        $comment = parent::comment('scientific_achievement', $id);

        $this->arrReturn = parent::getReturnArr(4, 1, 3);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['cate_arr'] = $this->cate_arr;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];
        $this->arrReturn['order_news'] = parent::getOrderNewsList();
        $this->arrReturn['read'] = empty($read) ? 0 : $read;
        $this->arrReturn['is_like'] = !empty($is_like) ? 1 : 0;
        $this->arrReturn['history_id'] = !empty($history_id) ? $history_id : 0;
        $this->arrReturn['num'] = !empty($comment['num']) ? $comment['num'] : 0;
        $this->arrReturn['commentList'] = !empty($comment['commentList']) ? $comment['commentList'] : [];
        $this->arrReturn['cid'] = $id;
        $this->arrReturn['cate'] = 'scientific_achievement';

        return view('', $this->arrReturn);
    }
}