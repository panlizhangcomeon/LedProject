<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use think\Cache;
use think\Session;
use vae\controller\ControllerIndex;
use think\Db;

class IndustryNewsController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $industry_news = Db::name('industry_news')->order('publish_date', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(6, 3, 0);
        $this->arrReturn['industry_news'] = $industry_news;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }

    public function show()
    {
        $id = (int)vae_get_param('id');

        $data = Db::name('industry_news')->find(['id' => $id]);

        $tid = $data['tid'];

        $read = parent::addRead($tid); //增加阅读量

        parent::addHistory($data['title'], $id, $data['publish_date'], 'industry_news'); //增加历史纪录

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('industry_news')->field('id')->order('publish_date', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'industry_news');

        //获得用户收藏信息
        $history = parent::getHistory($data['title'], $id, 'industry_news');
        if (!empty($history)) {
            $is_like = $history['is_like'];
            $history_id = $history['id'];
        }

        //获得文章评论
        $comment = parent::comment('industry_news', $id);

        $this->arrReturn = parent::getReturnArr(6, 3, 0);
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
        $this->arrReturn['cate'] = 'industry_news';

        return view('', $this->arrReturn);
    }
}