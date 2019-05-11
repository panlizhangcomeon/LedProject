<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-18
 */
namespace app\admin\validate;

use think\Validate;

class LeadershipWindow extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'job' => 'require',
        'avatar' => 'require',
        'desc' => 'require',
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '领导姓名必须',
        'job.require' => '领导职务必须',
        'avatar.number' => '头像必须',
        'desc.require' => '个人简介必须',
    ];

    protected $scene = [
        'add' => ['name', 'job', 'avatar', 'desc'],
        'edit' => ['id', 'name', 'job', 'avatar', 'desc']
    ];
}