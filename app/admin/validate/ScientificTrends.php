<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class ScientificTrends extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'author' => 'require',
        'source' => 'require',
        'publish_day' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '文章标题必须',
        'author.require' => '作者必须',
        'source.require' => '来源必须',
        'publish_day.require' => '发布时间必须',
        'content.require' => '内容必须',
    ];

    protected $scene = [
        'add' => ['title', 'author', 'source', 'publish_day', 'content'],
        'edit' => ['id', 'title', 'author', 'source', 'publish_day', 'content']
    ];
}