<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-03-21
 */
namespace app\index\controller;

use vae\controller\ControllerIndex;
use think\Db;

class AssociationAlbumController extends ControllerIndex
{
    public $arrReturn; //返回视图的数组

    public function index()
    {
        $flag = vae_get_param('flag');

        $association_album = Db::name('association_album')->select();

        $this->arrReturn = parent::getReturnArr(3, 0, 3);
        $this->arrReturn['association_album'] = $association_album;
        $this->arrReturn['flag'] = $flag;

        return view('', $this->arrReturn);
    }
}