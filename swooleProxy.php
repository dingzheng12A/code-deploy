<?php
/**
 * 消息中转服务
 * User: myf
 * Date: 2017/11/2
 * Time: 17:28
 */

//本机tcp的配置
$clients = [];
$list = [
    ['host'=>'192.168.31.142','port'=>'8903'],
    ['host'=>'192.168.31.142','port'=>'8913'],
];

foreach ($list as $item){

    $cli = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

    $remote = sprintf("%s-%s",$item['host'],$item['port']);
    $clients[$remote]=$cli;

    $cli->on("connect", function($cli) use($item) {
        //签到
        $initData = [
            'cmd'=>'sign',
            'host'=>$item['host'],
            'port'=>$item['port'],
        ];
        plog(sprintf("ClientTcp连接成功 [%s]",json_encode($initData)));
        $cli->send(json_encode($initData));
    });

    $cli->on("receive", function($cli, $data){
        echo sprintf("接受到消息转发请求:[%s]",$data);
        //实现消息转发
        $cmd = json_decode($data,true);
        global $clients;
        $remote = $cmd['remote'];

        $clients[$remote]->send($data);
    });

    $cli->on("close", function($cli) use($item){
        plog(sprintf("proxy连接关闭 close [%s-%s]",$item['host'],$item['port']));
    });

    $cli->on("error", function($cli) use($item){
        plog(sprintf("proxy连接出错error [%s-%s]",$item['host'],$item['port']));
    });

    $cli->connect($item['host'], $item['port'], 0.5);
}

//打印日志
function plog($str) {
    echo sprintf("[%s] %s\n", date("Y-m-d H:i:s"), $str);
}
