<?php
namespace Myf\Controller\WebSocket;

use Myf\Libs\WebSocketController;

/**
 * remark
 * User: myf
 * Date: 2017/10/26
 * Time: 07:21
 */
class TestController extends WebSocketController
{

    public function swooleAction($param){
        $fd = $param['fd'];
        $res = ['hello'];
        $this->success($fd,$res);
    }

}