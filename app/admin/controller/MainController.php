<?php
// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +---------------------------------------------------------------------
namespace app\admin\controller;

use vae\controller\AdminCheckLogin;
use think\Db;
use think\cache\driver\Redis;

class MainController extends AdminCheckLogin
{
    public function index()
    {
        $city = '';
        $adminMainHook = vae_set_hook_one('admin_main');
        if(!empty($adminMainHook)) {
        	return $adminMainHook;
        }

        $data = getPvUvIp();krsort($data);

        $arr_x = array_column($data, 'day');
        $arr_y_pv = array_column($data, 'pv_count');
        $arr_y_uv = array_column($data, 'uv_count');
        $arr_y_ip = array_column($data, 'ip_count');

        foreach ($arr_x as $value) {
            $data_x[] = '"' . $value . '"';
        }
        foreach ($arr_y_pv as $pv) {
            $data_y_pv[] = '"' . $pv . '"';
        }
        foreach ($arr_y_uv as $uv) {
            $data_y_uv[] = '"' . $uv . '"';
        }
        foreach ($arr_y_ip as $ip) {
            $data_y_ip[] = '"' . $ip . '"';
        }

        $coordinate_x = implode(',', $data_x);
        $coordinate_y_pv = implode(',', $data_y_pv);
        $coordinate_y_uv = implode(',', $data_y_uv);
        $coordinate_y_ip = implode(',', $data_y_ip);
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

        $total_pv = Db::name('pv')->sum('page_view'); //总pv

        $total_uv = Db::name('uv')->sum('uv'); //总uv

        $total_ip_arr = Db::query("select COUNT(DISTINCT client_ip) as ip from vae_pv");
        $total_ip = $total_ip_arr[0]['ip']; //总ip

        $total_user = Db::name('user')->count();

        return $this->fetch('', [
            'x' => $coordinate_x,
            'y_pv' => $coordinate_y_pv,
            'y_uv' => $coordinate_y_uv,
            'y_ip' => $coordinate_y_ip,
            'weather' => $weather,
            'total_pv' => $total_pv,
            'total_uv' => $total_uv,
            'total_ip' => $total_ip,
            'total_msg' => $total_user
        ]);
    }
}
