<?php
/**
 * 授权
 * User: myf
 * Date: 2017/11/1
 * Time: 21:00
 */

namespace Myf\Controller\WebSocket;


use Myf\Libs\Redis;
use Myf\Libs\WebSocketController;

class AuthController extends WebSocketController
{

    /**
     * webSocket连接认证
     */
    public function loginAction($param){
        $data = $param['param'];
        $user = $data['user'];
        $fd = $param['fd'];
        if(in_array($user,['myf','test'])){
            $userId = rand(10000,99999);
            $res = [
                'token'=>rand(100000000,99999999),
                'userId'=>$userId
            ];
            $redis = Redis::getInstance();
            $connUid = $this->getConnectUid($fd);
            $redis->set($userId,$connUid);
            $redis->set($connUid,$userId);

            //正在处理的tag
            $tag = $redis->get('vnnox_tag');
            if(empty($tag)){
                $tag = '';
            }
            $res['tag']=$tag;

            //上线的用户
            $userIdArr = [];
            $userIds = $redis->get('selfBuildUserIds');
            if(!empty($userIds)){
                $userIdArr = json_decode($userIds,true);
            }
            $userIdArr[]=$userId;

            $redis->set('selfBuildUserIds',json_encode($userIdArr));

            $this->success($fd,$res);
        }else{
            //ß$this->getWebSocket()->close($fd);
        }
    }

    /**
     * 连接断开
     * @param $param
     */
    public function quitAction($param){
        $fd = $param['fd'];
        $connUid = $this->getConnectUid($fd);
        $redis = Redis::getInstance();
        $userId = $redis->get($connUid);
        if($userId>0){
            $redis->del($userId);
            $redis->del($connUid);

            $userIdStr = $redis->get('selfBuildUserIds');
            if(!empty($userIdStr)){
                $userIdArr = json_decode($userIdStr,true);
                foreach ($userIdArr as $k=> $uid){
                    if($uid == $userId){
                        unset($userIdArr[$k]);
                        break;
                    }
                }
                $redis->set('selfBuildUserIds',json_encode($userIdArr));
            }
        }
    }

}