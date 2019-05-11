<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class ScientificProject extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'from' => 'require',
        'director' => 'require',
        'start_date' => 'require',
        'end_date' => 'require',
        'desc' => 'require',
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '项目名称必须',
        'from.require' => '项目来源必须',
        'director.require' => '项目负责人必须',
        'start_date.require' => '项目开始时间必须',
        'end_date.require' => '项目结束时间必须',
        'desc.require' => '项目简介必须必须',
    ];

    protected $scene = [
        'add' => ['name', 'from', 'director', 'start_date', 'end_date', 'desc'],
        'edit' => ['id', 'name', 'from', 'director', 'start_date', 'end_date', 'desc']
    ];
}