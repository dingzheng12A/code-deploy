<?php

namespace Myf\Controller\Http;

use Myf\Libs\HttpController;

/**
 * remark
 * User: myf
 * Date: 2017/11/1
 * Time: 21:16
 */
class TestController extends HttpController
{

    public function mainAction(){
        $ws = $this->getWebSocket();
        $get = $this->getRequest()->get;
//        foreach($ws->connections as $fd)
//        {
//            $ws->push($fd, sprintf("hello, you have a message from request=[%s] \n",json_encode($get)));
//        }
        $userId = $get['userId'];
        $this->pushToUser($userId,"我是来自另一个websocket服务的！");
        $this->response('success');
    }

    public function pageAction(){
        $this->display();
    }

}