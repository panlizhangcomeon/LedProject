<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class GeneralProfile extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'img' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '标题必须',
        'img.require' => '图片必须',
        'content.require' => '内容必须',
    ];

    protected $scene = [
        'add' => ['title', 'img', 'content'],
        'edit' => ['id', 'title', 'img', 'content']
    ];
}