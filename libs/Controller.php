<?php
/**
 * 基础Controller
 * User: myf
 * Date: 17/2/23
 * Time: 上午10:31
 */

namespace Myf\Libs;


class Controller {

    private $ws;

    public function _init($ws){
        $this->ws = $ws;
    }

    /**
     * 发送通用消息
     * @param int $fd
     * @param $msg
     * @return bool 发送成功或失败
     */
    public function push($fd,$msg){
        $ok = false;
        if($this->ws->exist($fd)){
            $ok =  $this->ws->push($fd,$msg);
        }
        return $ok;
    }

    /**
     * 给制定的用户发送消息
     * @param $userId
     * @param $msg
     * @return bool
     */
    public function pushToUser($userId,$msg){
        $redis = Redis::getInstance();
        $connUid = $redis->get($userId);
        $res = false;
        if($connUid){
            list($ip, $port, $fd)  = explode('-',$connUid);
            $info = $this->getConnectInfo($fd);
            //连接在本机，直接发送消息
            if(isset($info) && $info['server_ip']==$ip && $info['server_port']==$port){
                $res = $this->push($fd,$msg);
            }else{
                //这里需要使用tcp的端口，作为remote的端口
                $wsConfig = config("swoole.TCPServer");
                //本机的ip+tcp端口
                $local = sprintf("%s-%s",getServerIp(),$wsConfig['port']);
                $proxyFd = $redis->get($local);
                //目标服务器的ip+tcp端口
                $remote = sprintf("%s-%s",$ip,$wsConfig['port']);
                if(!empty($proxyFd)){
                    $cmd = [
                        'cmd'=>'tran',
                        'remote'=>$remote,
                        'fd'=>$fd,
                        'msg'=>$msg,
                    ];
                    $this->ws->send($proxyFd,json_encode($cmd));
                }else{
                    echo "no cli\n";
                }
            }
        }
        return $res;
    }

    /**
     * 获取连接信息
     * @param $fd
     * @return mixed
     */
    public function getConnectInfo($fd){
        $info = null;
        if($this->ws->exist($fd)){
            $info = $this->ws->connection_info($fd);
            $info['server_ip']=getServerIp();
        }
        return $info;
    }

    /**
     * 获取连接唯一标识
     * @param $fd
     * @return string
     */
    public function getConnectUid($fd){
        $connInfo = $this->getConnectInfo($fd);
        $conn = null;
        if(isset($connInfo)){
            $conn = sprintf("%s-%s-%s",$connInfo['server_ip'],$connInfo['server_port'],$fd);
        }
        return $conn;
    }

    /**
     * 获取WebSocket句柄对象
     * @return mixed
     */
    public function getWebSocket(){
        return $this->ws;
    }

    /**
     * action前执行的全局方法，可继承并重构
     */
    public function _before_action() {
    }

    /**
     * action后执行的全局方法,可继承并重构
     */
    public function _after_action() {
    }


    /**
     * 魔术方法
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments) {
        echo sprintf("[%s] error url 404", $name);
    }


    public function makeUrl($controller, $action, $param = '') {
        $url = sprintf('%s/index.php?c=%s&a=%s&%s', getBasePath(), $controller, $action, $param);
        return $url;
    }

}