<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class AssociationAlbum extends Validate
{
    protected $rule = [
        'id' => 'require',
        'author' => 'require',
        'title' => 'require',
        'desc' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'author.require' => '作者必须',
        'title.require' => '图片标题必须',
        'desc.require' => '图片描述必须'
    ];

    protected $scene = [
        'add' => ['author', 'title', 'desc'],
        'edit' => ['id', 'author', 'title', 'desc']
    ];
}