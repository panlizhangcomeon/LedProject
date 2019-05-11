<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class ProductDisplay extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'desc' => 'require',
        'author' => 'require',
        'img' => 'require',
        'day' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '产品名称必须',
        'desc.require' => '产品描述必须',
        'author.require' => '作者必须',
        'img.require' => '产品图片必须',
        'day.require' => '发布时间必须'
    ];

    protected $scene = [
        'add' => ['name', 'desc', 'author', 'img', 'day'],
        'edit' => ['id', 'name', 'desc', 'author', 'img', 'day']
    ];
}