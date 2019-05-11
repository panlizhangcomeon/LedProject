<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class IndustryNews extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'source' => 'require',
        'editor' => 'require',
        'publish_date' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '新闻标题必须',
        'source.require' => '来源必须',
        'editor.require' => '编辑作者必须',
        'publish_date.require' => '发布时间必须',
        'content.require' => '内容必须',
    ];

    protected $scene = [
        'add' => ['title', 'source', 'editor', 'publish_date', 'content'],
        'edit' => ['id', 'title', 'source', 'editor', 'publish_date', 'content']
    ];
}