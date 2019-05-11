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
        $redis = $this->connectRedis();
        $arr = $redis->zRevRange('read', 0, -1, 'WITHSCORES');
        $order = array_keys($arr);
        for ($i = 0; $i<6; $i++) {
            $data[] = Db::name('search')->find(['id' => $order[$i]]);
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
}