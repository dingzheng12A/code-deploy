<?php
/**
 * remark
 * User: myf
 * Date: 2017/11/8
 * Time: 19:42
 */

namespace Myf\Controller\Http;


use Myf\Libs\HttpController;

class WsController extends HttpController
{

    public function pushAction(){
        $get = $this->getRequest()->get;
        $msg = $get['msg'];
        $userId = $get['userId'];
        $this->pushToUser($userId,$msg);
    }

    //发布
    public function releaseAction(){

    }

}