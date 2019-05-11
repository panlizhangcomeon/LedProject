<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-14
 */
namespace app\admin\validate;
use think\Validate;

class Association extends Validate
{
    protected $rule = [
        'title'            => 'require',
        'content'          => 'require',
        'id'               => 'require',
    ];

    protected $message = [
        'title.require'                 => '标题不能为空',
        'id.require'                    => '缺少更新条件',
        'content.require'               => '内容不能为空'
    ];

    protected $scene = [
        'edit' => ['title', 'content', 'id'],
    ];
}