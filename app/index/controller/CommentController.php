<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/13
 * Time: 15:03
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;
use think\Session;

class CommentController extends ControllerIndex
{
    public function addComment()
    {
        $param = vae_get_param();
        if (!Session::has('username')) {
            $data['error'] = "您未登陆，无法评论";
        } else {
            if (!empty($param['comment'])) {
                $cm = json_decode($param['comment'], true);
                $cm['comment_time'] = date('Y-m-d H:i:s');
                $id = Db::name('comment')->insertGetId($cm);
                $cm['id'] = $id;
                $data = $cm;
                $num = Db::name('comment')->where(['cate' => $cm['cate'], 'cid' => $cm['cid']])->count(); //统计评论总数
                $data['num'] = $num;
            } else {
                $data['error'] = "0";
            }
        }
        echo json_encode($data);
    }
}