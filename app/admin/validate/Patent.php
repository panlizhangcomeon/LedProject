<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-19
 */
namespace app\admin\validate;

use think\Validate;

class Patent extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'inventor' => 'require',
        'patent_number' => 'require',
        'year' => 'require',
        'enable_area' => 'require',
        'transfer_status' => 'require'
    ];

    protected $message = [
        'id.require' => '缺少更新条件',
        'name.require' => '专利名称必须',
        'inventor.require' => '发明人必须',
        'patent_number.require' => '专利号必须',
        'year.require' => '年份必须',
        'enable_area.require' => '授权地区必须',
        'transfer_status.require' => '转让情况必须'
    ];

    protected $scene = [
        'add' => ['name', 'inventor', 'patent_number', 'year', 'enable_area', 'transfer_status'],
        'edit' => ['id', 'name', 'inventor', 'patent_number', 'year', 'enable_area', 'transfer_status']
    ];
}