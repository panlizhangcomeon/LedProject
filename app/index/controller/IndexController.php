<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-17
 */
namespace app\index\controller;

use think\Cookie;
use think\Session;
use vae\controller\ControllerIndex;
use think\Db;

class IndexController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        //导航栏
        $nav = Db::name('nav')->select();

        //首页轮播图
        $slide_info = getSlideInfo('VAE_INDEX_SLIDE');

        //产品展示
        $product_display = Db::name('product_display')->limit(8)->order('updated_at', 'desc')->select();

        //行内新闻
        $industry_news = Db::name('industry_news')->limit(6)->order('publish_date', 'desc')->select();

        //天气信息
        $weather = parent::getWeather();

        $this->arrReturn['nav_info'] = getNavInfo($nav);
        $this->arrReturn['slide_info'] = $slide_info;
        $this->arrReturn['product_display'] = $product_display;
        $this->arrReturn['industry_news'] = $industry_news;
        $this->arrReturn['weather'] = $weather;

        return $this->view->fetch('index', $this->arrReturn);
    }

    /**
     * 全站搜索
     */
    public function search()
    {
        $search_word = vae_get_param('search_word');

        if (empty($search_word)) {
            $this->error('请输入标题关键字');
        }

        $flag = vae_get_param('flag');

        $data = Db::name('search')->where('title', 'like', '%' . $search_word . '%')->paginate(20);

        $this->arrReturn = parent::getReturnArr(6, 3, 1);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['search_word'] = $search_word;
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);

    }

    /**
     * @desc 保存uv
     * @return string
     * @throws \think\Exception
     */
    public function saveUv()
    {
        $now = date('Y-m-d');
        $now_time = date('Y-m-d H:i:s');
        $data = Db::name('uv')->where(['day' => $now])->find();
        $message = ['code' => 1, 'msg' => '成功'];
        if (!empty($data)) {
            $uv = $data['uv'];
            $id = $data['id'];
            $reg = Db::name('uv')->update([
                'id' => $id,
                'uv' => $uv+1,
                'updated_at' => $now_time
            ]);
            if ($reg) {
                Cookie::set('time', date('Y-m-d'), ['path' => '/','expire' => strtotime(date('Y-m-d', time()+86400))-time()]);
            } else {
                $message['code'] = 0;
                $message['msg'] = '更新失败';
            }
        } else {
            $reg = Db::name('uv')->insert([
                'uv' => 1,
                'day' => $now,
                'created_at' => $now_time,
                'updated_at' => $now_time
            ]);
            if ($reg) {
                Cookie::set('time', date('Y-m-d'), ['path' => '/','expire' => strtotime(date('Y-m-d', time()+86400))-time()]);
            } else {
                $message['code'] = 0;
                $message['msg'] = '新增失败';
            }
        }

        return json_encode($message);
    }

    /**
     * @desc 保存 IP 和 IP对应的pv
     * @throws \think\Exception
     */
    public function saveIp()
    {
        $ip_string = get_client_ip();
        $ip  = ip2long($ip_string); //真实ip地址
        $now = date('Y-m-d H:i:s');
        $now_day = date('Y-m-d');
        $where = [
            'client_ip' => $ip,
            'day' => $now_day
        ];
        $data = Db::name('pv')->where($where)->find();
        $message = ['code' => 1, 'msg' => '成功'];
        if (!empty($data)) {
            $page_view = $data['page_view'];
            $id = $data['id'];
            $reg = Db::name('pv')->update([
                'id' => $id,
                'page_view' => $page_view+1,
                'updated_at' => $now
            ]);
            if (!$reg) {
                $message['code'] = 0;
                $message['msg'] = '更新失败';
            }
        } else {
            $reg = Db::name('pv')->insert([
                'page_view' => 1,
                'client_ip' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
                'day' => $now_day
            ]);
            if (!$reg) {
                $message['code'] = 0;
                $message['msg'] = '新增失败';
            }
        }

        return json_encode($message);
    }

    public function test()
    {
        return 'hello world';
    }

    //添加一个值
    public function addOneValue()
    {
        Session::clear();
        Session::set('value1',1);
    }

    //添加多个值
    public function addTwoValue()
    {
        Session::clear();
        Session::set('value2',2);
        Session::set('value3',3);
    }

    //响应请求
    public function Response()
    {
        return 'test';
    }
}