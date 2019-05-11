<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class ScientificAchievement extends Validate
{
    protected $rule = [
        'id' => 'require',
        'cate' => 'require',
        'name' => 'require',
        'desc' => 'require',
        'function' => 'require',
        'day' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'cate.require' => '分类必须',
        'name.require' => '成果名称必须',
        'desc.require' => '成果简介必须',
        'function.require' => '功能简介必须',
        'day.require' => '发布时间必须'
    ];

    protected $scene = [
        'add' => ['cate', 'name', 'desc', 'function', 'day'],
        'edit' => ['id', 'cate', 'name', 'desc', 'function', 'day']
    ];
}