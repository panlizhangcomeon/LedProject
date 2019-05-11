<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-07
 */
namespace app\admin\validate;

use think\Validate;

class TechnicalConsultation extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'content' => 'require',
        'tel' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'title.require' => '咨询标题不能为空',
        'content.require' => '咨询内容不能为空',
        'tel.require' => '联系电话不能为空'
    ];

    protected $scene = [
        'add' => ['title', 'content', 'tel'],
        'edit' => ['id', 'title', 'content', 'tel']
    ];
}