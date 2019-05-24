<?php
// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------
namespace vae\controller;
use think\Controller;
use think\Request;
use think\Hook;
use think\Db;
use think\cache\driver\Redis;
use think\Config;
use think\Session;

class ControllerIndex extends Controller
{
    public $arrReturn; //渲染到视图的数组

    public $cateArr = ['industry_news', 'central_information', 'announcement', 'policy_broadcast', 'scientific_trends', 'scientific_achievement', 'research_consulation']; //文章不同栏目

    protected function _initialize()
    {
        parent::_initialize();

        //Hook::listen('calculate_PV');
        //Hook::listen('judge_visitor');
    }

    /**
     * @desc 返回渲染视图的数组
     * @param $nav_id
     * @param $cate_num
     * @param $column_num
     * @return array
     */
    public function getReturnArr($nav_id, $cate_num, $column_num)
    {
        $nav = Db::name('nav')->select();
        $column = Db::name('nav_info')->where(['nav_id' => $nav_id])->select();
        $this->arrReturn = [
            'nav_info' => getNavInfo($nav),
            'cate' => $nav[$cate_num]['title'],
            'column' => $column,
            'name' => $column[$column_num]['title']
        ];
        return $this->arrReturn;
    }

    /**
     * @desc 获得城市天气信息
     * @return mixed
     */
    public function getWeather()
    {
        $redis = new Redis();
        $ip = get_client_ip();
        $area = getIpInfo($ip);  //使用了百度IP定位API
        if ($area['status'] == 1) {
            $city = $area['result']['content']['address_detail']['city']; //所在城市
        }
        $now = date('Y-m-d_H');
        $cache_weather = $redis->get($now . '_WEATHER' . '_' . $city);

        if (!empty($cache_weather)) {
            $weather = $cache_weather;
        } else {
            $weather = getTodayWeather($city);
            $redis->set($now . '_WEATHER' . '_' . $city, $weather, 3600);
        }
        return $weather;
    }

    /**
     * @desc 获得按照阅读量排序的全站咨询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOrderNewsList()
    {
        $data = [];
        $redis = $this->connectRedis();
        $arr = $redis->zRevRange('read', 0, -1, 'WITHSCORES');
        $order = array_keys($arr);
        if (!empty($order)) {
            for ($i = 0; $i<6; $i++) {
                $data[] = Db::name('search')->find(['id' => $order[$i]]);
            }
        }
        return $data;
    }

    /**
     * @desc 连接 redis
     * @return \Redis
     */
    protected function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(Config::get("REDIS_HOST"), Config::get("REDIS_PORT"));
        return $redis;
    }

    /**
     * @desc 增加阅读量
     * @param $tid
     * @return float
     */
    protected function addRead($tid)
    {
        $redis = $this->connectRedis();
        $read = $redis->zScore('read', $tid);
        if ($read) {
            $redis->zIncrBy('read', 1, $tid);
        } else {
            $redis->zAdd('read', 1, $tid);
        }
        return $read;
    }

    /**
     * @desc 增加浏览历史记录
     * @param $title
     * @param $id
     * @param $publish_date
     */
    protected function addHistory($title, $id, $publish_date, $cate)
    {
        if (Session::has('username')) {
            $username = Session::get('username');
            $param = ['username' => $username, 'title' => $title, 'cate' => $cate, 'cid' => $id];
            $history = Db::name('history')->where($param)->find();
            if (empty($history)) {
                $param['publish_date'] = $publish_date;
                $param['created_at'] = date('Y-m-d H:i:s');
                $param['updated_at'] = date('Y-m-d H:i:s');
                Db::name('history')->insert($param);
            } else {
                Db::name('history')->update(['updated_at' => date('Y-m-d H:i:s'), 'id' => $history['id']]);
            }
        }
    }

    /**
     * @desc 获得是否收藏
     * @param $title
     * @param $id
     * @param $cate
     * @return bool|mixed
     */
    protected function getHistory($title, $id, $cate)
    {
        if (Session::has('username')) {
            $username = Session::get('username');
            $param = ['username' => $username, 'title' => $title, 'cate' => $cate, 'cid' => $id];
            $data = Db::name('history')->where($param)->find();
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @desc 递归获得文章对应评论列表
     * @param $cate
     * @param $cid
     * @param int $parent_id
     * @param array $result
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    protected function getCommentList($cate, $cid, $parent_id = 0, &$result = [])
    {
        $arr = Db::name('comment')->where([
            'cate' => $cate,
            'cid' => $cid,
            'parent_id' => $parent_id
        ])->order('comment_time', 'desc')->select();
        if (empty($arr)) {
            return $arr;
        }
        foreach ($arr as $cm) {
            $thisArr = &$result[];
            $cm['children'] = $this->getCommentList($cate, $cid, $cm['id'], $thisArr);
            $thisArr= $cm;
        }
        return $result;
    }

    /**
     * @desc 获得评论总数和列表
     * @param $cate
     * @param $cid
     * @return array
     */
    public function comment($cate, $cid)
    {
        $num = Db::name('comment')->where(['cate' => $cate, 'cid' => $cid])->count(); //获取评论总数
        $data = $this->getCommentList($cate, $cid); //获取评论列表
        return ['num' => $num, 'commentList' => $data];
    }


    //获得最近十个登陆的用户
    public function getRecentUser()
    {
        $data = Db::name('user')->order('last_login', 'desc')->field('username')->limit(1, 10)->select();
        $recent_users = array_column($data, 'username');
        return $recent_users;
    }

    //计算当前用户和最近十个用户不同栏目的浏览文章数,根据欧式距离计算用户相似度,得出相似度最近的用户
    public function getSimilarUser()
    {
        $data = [];
        $cates = $this->cateArr;
        $users = $this->getRecentUser();
        if (Session::has('username')) {
            $username = Session::get('username');
            foreach ($users as $u) {
                foreach ($cates as $c) {
                    $data[$username][$c] = Db::name('history')->where(['cate' => $c, 'username' => $username])->count();
                    $data[$u][$c] = Db::name('history')->where(['cate' => $c, 'username' => $u])->count();
                    $similar[$u][] = pow(($data[$username][$c]-$data[$u][$c]), 2);
                }
            }
            $result = getSimilarData($similar);
            $users = array_search(min($result), $result);
            return $users;
        }
    }

    //组装历史浏览数据
    public function getPackHistoryData($arr)
    {
        foreach ($arr as $value) {
            $data[] = [
                'title' => $value['title'],
                'cate' => $value['cate'],
                'cid' => $value['cid'],
                'publish_date' => $value['publish_date']
            ];
        }
        return !empty($data) ? $data : [];
    }

    //获得推荐内容
    public function getRecommend()
    {
        if (Session::has('username')) {
            $login_user = Session::get('username');
            $similar_user = $this->getSimilarUser();
            $similar_user_data = Db::name('history')->where(['username' => $similar_user])->order('updated_at', 'desc')->limit(10)->select();
            $login_user_data = Db::name('history')->where(['username' => $login_user])->select();
            $similar_data = $this->getPackHistoryData($similar_user_data);
            $login_data = $this->getPackHistoryData($login_user_data);
            $recommend_data = array_diff_assoc2_deep($similar_data, $login_data);
            return $recommend_data;
        }
    }
}