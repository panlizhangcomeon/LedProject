<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/18
 * Time: 0:33
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Session;
use think\Cache;

class RecommendController extends ControllerIndex
{
    public $arrReturn = [];//返回渲染视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        //用户登陆后获得推荐文章，五分钟一次推荐
        $recommend = parent::getRecommend();

        $this->arrReturn = parent::getReturnArr(3, 0, 0);
        $this->arrReturn['recommend'] = !empty($recommend) ? $recommend : [];
        $this->arrReturn['flag'] = $flag;
        $this->arrReturn['order_news'] = parent::getOrderNewsList();

        return view('', $this->arrReturn);
    }
}