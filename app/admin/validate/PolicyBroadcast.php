<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class PolicyBroadcast extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'publish_date' => 'require',
        'source' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '播报标题必须',
        'publish_date.require' => '播报发布日期必须',
        'source.require' => '播报来源必须',
        'content.require' => '播报内容必须',
    ];

    protected $scene = [
        'add' => ['title', 'publish_date', 'source', 'content'],
        'edit' => ['id', 'title', 'publish_date', 'source', 'content']
    ];
}