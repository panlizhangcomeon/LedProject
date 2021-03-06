<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 12:19
 */
namespace app\index\controller;
use vae\controller\ControllerIndex;
use think\Db;

class ResearchConsulationController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $scientific_trends = Db::name('research_consulation')->order('publish_day', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(4, 1, 6);
        $this->arrReturn['research_consulation'] = $scientific_trends;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    public function show()
    {
        $id = vae_get_param('id');

        $data = Db::name('research_consulation')->find(['id' => $id]);

        $tid = $data['tid'];

        $read = parent::addRead($tid); //增加阅读量

        parent::addHistory($data['title'], $id, $data['publish_day'], 'research_consulation'); //增加历史纪录

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('research_consulation')->field('id')->order('publish_day', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'research_consulation');

        //获得用户收藏信息
        $history = parent::getHistory($data['title'], $id, 'research_consulation');
        if (!empty($history)) {
            $is_like = $history['is_like'];
            $history_id = $history['id'];
        }

        //获得文章评论
        $comment = parent::comment('research_consulation', $id);

        $this->arrReturn = parent::getReturnArr(4, 1, 6);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];
        $this->arrReturn['order_news'] = parent::getOrderNewsList();
        $this->arrReturn['read'] = empty($read) ? 0 : $read;
        $this->arrReturn['is_like'] = !empty($is_like) ? 1 : 0;
        $this->arrReturn['history_id'] = !empty($history_id) ? $history_id : 0;
        $this->arrReturn['num'] = !empty($comment['num']) ? $comment['num'] : 0;
        $this->arrReturn['commentList'] = !empty($comment['commentList']) ? $comment['commentList'] : [];
        $this->arrReturn['cid'] = $id;
        $this->arrReturn['cate'] = 'research_consulation';

        return view('', $this->arrReturn);
    }
}