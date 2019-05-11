<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-22
 */
namespace app\index\controller;

use think\Session;
use vae\controller\ControllerIndex;
use think\Db;

class DocumentCenterController extends ControllerIndex
{
    public $arrReturn; //返回渲染视图的数组

//    public function index()
//    {
//        $flag = vae_get_param('flag');
//        $data['content'] = '';
//        $pid = vae_get_param('pid');
//        $id = vae_get_param('id');
//        if (is_null($pid)) {
//            $pid = 0;
//        }
//
//        $catalog = Db::name('document_center')->where(['pid' => $pid])->paginate(20);
//
//        $data = Db::name('document_center')->where(['id' => $pid])->find();
//        $title = Db::name('document_center')->where(['id' => $id])->find();
//
//        $this->arrReturn = parent::getReturnArr(7, 4, 0);
//        $this->arrReturn['data'] = $data;
//        $this->arrReturn['catalog'] = $catalog;
//        $this->arrReturn['title'] = $title;
//        $this->arrReturn['flag'] = $flag;
//
//        return view('', $this->arrReturn);
//    }

    //获得文档列表
    public function index()
    {
        $flag = vae_get_param('flag');
        $arr_doc = my_dir(ROOT_PATH . 'public/upload/docs');
        $this->arrReturn = parent::getReturnArr(7, 4, 0);
        $this->arrReturn['docs'] = $arr_doc;
        $this->arrReturn['flag'] = $flag;
        return view('', $this->arrReturn);
    }

    //下载文件，判断是否会员
    public function download()
    {
        $data = vae_get_param();
        $locate = '118.25.4.125';
        $path = 'upload/docs/' . $data['locate'];
        $username = Session::get('username');
        $data = Db::name('user')->where(['username' => $username])->find();
        $is_vip = $data['is_vip'];
        if ($is_vip) {
            if(file_exists($path)){
                header('Location:http://' . $locate . '/' . $path);exit();
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        } else {
            $this->error('您还不是会员，无法下载本站文档');
        }
    }
}