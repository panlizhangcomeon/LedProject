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
namespace plugin\hacker;
use vae\lib\Plugin;

class HackerIndex extends Plugin
{
    public $explain = [
        'name'        => 'Hacker',
        'hook'        => 'admin_login',
        'title'       => '[黑客]后台登录页',
        'desc'        => '自定义黑客风格后台登录页',
        'author'      => '听雨',
        'interface'   => 0,
    ];

    public function index($params)
    {
        return $this->share('index');
    }

    //必须实现配置
    public function setConfig()
    {
        return true;
    }

    //必须实现安装
    public function install()
    {
        return true;
    }

    //必须实现卸载
    public function uninstall()
    {
        return true;
    }
}