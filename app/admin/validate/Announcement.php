<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class Announcement extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'day' => 'require',
        'source' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '公告标题必须',
        'day.require' => '公告发布日期必须',
        'source.require' => '公告来源必须',
        'content.require' => '公告内容必须',
    ];

    protected $scene = [
        'add' => ['title', 'day', 'source', 'content'],
        'edit' => ['id', 'title', 'day', 'source', 'content']
    ];
}