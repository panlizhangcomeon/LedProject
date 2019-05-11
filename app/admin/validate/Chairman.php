<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-17
 */
namespace app\admin\validate;

use think\Validate;

class Chairman extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|max:25',
        'job' => 'require',
        'native' => 'require',
        'phone' => 'require|number'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '姓名不能为空',
        'name.max' => '姓名不能超过25个字符',
        'job.require' => '职务不能为空',
        'native.require' => '籍贯不能为空',
        'phone.require' => '联系方式不能为空',
        'phone.number' => '联系方式必须为数字',
    ];

    protected $scene = [
        'add' => ['name', 'job', 'native', 'phone'],
        'edit' => ['id', 'name', 'job', 'native', 'phone']
    ];
}