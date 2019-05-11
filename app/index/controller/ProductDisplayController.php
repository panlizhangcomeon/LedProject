<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class ProductDisplayController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');
        $policy_broadcast = Db::name('product_display')->order('day', 'desc')->paginate(20);

        $this->arrReturn = parent::getReturnArr(4, 1, 5);
        $this->arrReturn['product_display'] = $policy_broadcast;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }

    public function show()
    {
        $id = vae_get_param('id');

        $data = Db::name('product_display')->find(['id' => $id]);

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('product_display')->field('id')->order('day', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'product_display');

        $this->arrReturn = parent::getReturnArr(6, 3, 3);

        $this->arrReturn['data'] = $data;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];

        return view('', $this->arrReturn);
    }
}