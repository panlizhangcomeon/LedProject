<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-07
 */
namespace app\admin\validate;

use think\Validate;

class ResearchConsulation extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publish_day' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '咨询标题不能为空',
        'content.require' => '咨询内容不能为空',
        'publish_day.require' => '发布日期不能为空'
    ];

    protected $scene = [
        'add' => ['title', 'content', 'publish_day'],
        'edit' => ['id', 'title', 'content', 'publish_day']
    ];
}