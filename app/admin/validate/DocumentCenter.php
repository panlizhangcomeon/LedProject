<?php
// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +---------------------------------------------------------------------
namespace app\admin\validate;
use think\Validate;
use think\Db;

class DocumentCenter extends Validate
{
    protected $rule = [
        'title'       => 'require',
        'pid'         => 'require',
        'id'          => 'require',
    ];

    protected $message = [
        'title.require'      => '名称不能为空',
        'pid.require'        => '父级标题为必选',
        'title.unique'       => '同样的记录已经存在!',
        'id.require'         => '缺少更新条件',
        'field.require'      => '缺少要更新的字段名',
    ];

    protected $scene = [
        'add'  => ['title', 'pid'],
        'edit' => ['id', 'content', 'title.unique'],
        'editList' => ['id', 'field']
    ];
}