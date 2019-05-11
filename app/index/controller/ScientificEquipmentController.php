<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-21
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class ScientificEquipmentController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');
        $scientific_equipment = Db::name('scientific_equipment')->order('updated_at', 'desc')->paginate(20);


        $this->arrReturn = parent::getReturnArr(4, 1, 0);
        $this->arrReturn['scientific_equipment'] = $scientific_equipment;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }

    /**
     * 科研设备详情
     * @return \think\response\View
     */
    public function show()
    {
        $id = (int)vae_get_param('id');

        $data = Db::name('scientific_equipment')->where(['id' => $id])->find();

        //查找出全部id，组合一个数组，根据索引去判断上一个或下一个是否存在
        $all_id = Db::name('scientific_equipment')->field('id')->order('updated_at', 'desc')->select();

        $pre_next_data = getPreNextData($all_id, $id, 'scientific_equipment');

        //天气信息
        $weather = parent::getWeather();

        $this->arrReturn = parent::getReturnArr(4, 1, 0);
        $this->arrReturn['data'] = $data;
        $this->arrReturn['data_pre'] = $pre_next_data['data_pre'];
        $this->arrReturn['data_next'] = $pre_next_data['data_next'];
        $this->arrReturn['weather'] = $weather;

        return view('', $this->arrReturn);
    }
}