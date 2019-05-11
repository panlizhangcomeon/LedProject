<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 14:18
 */
namespace app\index\controller;

use think\Db;
use vae\controller\ControllerIndex;
use think\Session;
use think\Validate;

class LeaveMessageController extends ControllerIndex
{
    public $arrReturn = [];//返回渲染视图的数组

    public function index()
    {
        $data = Db::name('leave_message')->where(['status' => 1])->paginate(20);

        $this->arrReturn = parent::getReturnArr(3, 0, 0);
        $this->arrReturn['data'] = $data;

        return view('', $this->arrReturn);
    }

    public function add()
    {
        $param = vae_get_param();
        if (empty($param)) {
            echo "<script>
alert('请勿直接访问本页面');
location.href='/index/leave_message/index?flag=message';
</script>";
        }
        $result = $this->validate($param, 'app\index\validate\Code');
        if ($result !== true) {
            echo "<script>
alert('验证码不正确');
history.back(-1);
</script>";
        } else {
            unset($param['captcha']);
            $now = date('Y-m-d H:i:s');
            $param['created_at'] = $now;
            $param['updated_at'] = $now;
            $reg = Db::name('leave_message')->insert($param);
            if ($reg) {
                echo "<script>
alert('您的留言已成功提交，等待管理员审核');
location.href = '/index/index/index';
</script>";
            } else {
                echo "<script>
alert('留言提交失败');
history.back(-1);
</script>";
            }
        }
    }
}