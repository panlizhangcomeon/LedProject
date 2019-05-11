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
namespace app\index\validate;
use think\Validate;
use think\Db;

class Code extends Validate
{
    protected $rule = [
        'captcha'        => 'captcha',
    ];

    protected $message = [
        'captcha.captcha'        => '验证码不正确',
    ];
}