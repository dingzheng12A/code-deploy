<?php
/**
 * remark
 * User: myf
 * Date: 2017/11/7
 * Time: 15:17
 */

namespace Myf\Controller;


use Myf\Libs\MvcController;
use Myf\Model\VersionModel;

class IndexController extends MvcController
{
    public function indexAction(){
        $vModel = new VersionModel();
        $versions = $vModel->findAll();
        $this->assign('rows',$versions);
        $this->display();
    }

}