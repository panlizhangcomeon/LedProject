<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-14
 */
namespace app\admin\validate;

use think\Validate;

class OrganizationStructure extends Validate
{
    protected $rule = [
        'title' => 'require',
        'id' => 'require',
        'img' => 'require'
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'id.require' => '缺少更新条件',
        'img.require' => '图片不能为空'
    ];

    protected $scene = [
        'edit' => ['title', 'id', 'img']
    ];
}