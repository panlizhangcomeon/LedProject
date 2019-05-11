<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-02-13
 */
namespace app\admin\controller;

use vae\controller\ControllerBase;
use think\Db;
use think\Loader;
use think\cache\driver\Redis;

class DocumentCenterController extends ControllerBase
{
    public function index()
    {
        return view();
    }

    //菜单列表
    public function getMenuList()
    {
        $arr_doc = my_dir(ROOT_PATH . 'public/upload/docs');
        $i = 0;
        $data = [];
        foreach ($arr_doc as $key => $value) {
            foreach ($value as $doc) {
                $data[$i]['name'] = $doc;
                $data[$i]['date'] = $key;
                $i++;
            }
        }
        return vae_assign(0, '', $data);
    }

    //上传文档页面
    public function add()
    {
        return view();
    }

    /**
     * 上传文档
     */
    public function addSubmit()
    {
        //获取表单上传文件
        $file = request()->file('doc');
        if (empty($file)) {
            $this->error('您未上传任何文档');
        }
        $date = date('Y-m-d');
        $info = $file->validate(['size' => 10000000, 'ext' => 'doc,docx,xlsx,xls'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'docs' . DS . $date, '');
        if ($info) {
            echo "<script>
alert('文档上传成功');
history.back(-1);
</script>";
        } else {
            $msg = $file->getError();
            echo "<script>
alert('" . $msg . "');
history.back(-1);
</script>";
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = vae_get_param();
        $name = $data['name'];
        $date = $data['date'];
        $path = 'upload/docs/' . $date . '/' . $name;
        $reg = unlink($path);
        if ($reg) {
            return vae_assign(1,"删除文件成功");
        } else {
            return vae_assign(0,"删除失败！");
        }
    }

}