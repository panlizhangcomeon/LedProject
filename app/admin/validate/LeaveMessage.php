<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 15:52
 */
namespace app\admin\validate;

use think\Validate;

class LeaveMessage extends Validate
{
    protected $rule = [
        'reply'        => 'require',
    ];

    protected $message = [
        'reply.require'        => '回复不能为空',
    ];
}