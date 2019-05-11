<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-18
 */
namespace app\admin\validate;

use think\Validate;

class JoinUs extends Validate
{
    protected $rule = [
        'id' => 'require',
        'department' => 'require',
        'tel' => 'require',
        'desc' => 'require',
        'locate' => 'require',
        'zip_code' => 'require|max:6'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'department.require' => '部门名称必须',
        'tel.require' => '电话必须',
        'desc.require' => '部门职能简介必须',
        'locate.require' => '部门地址必须',
        'zip_code.require' => '邮编必须',
        'zip_code.max' => '邮编最长为6位'
    ];

    protected $scene = [
        'add' => ['department', 'tel', 'desc', 'locate', 'zip_code'],
        'edit' => ['id', 'department', 'tel', 'desc', 'locate', 'zip_code']
    ];
}