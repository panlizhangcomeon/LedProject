<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class ScientificEquipment extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'desc' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '设备名称必须',
        'desc.require' => '设备描述必须'
    ];

    protected $scene = [
        'add' => ['name', 'desc'],
        'edit' => ['id', 'name', 'desc']
    ];
}