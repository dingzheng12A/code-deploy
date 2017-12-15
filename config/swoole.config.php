<?php
/**
 * swoole配置文件
 * User: myf
 * Date: 2017/11/2
 * Time: 10:39
 */
return [
    //WebSocket的ip及端口配置
    'WebSocket'=>[
        'host'=>'0.0.0.0',
        'port'=>9011,
        //配置
        'set'=>[
        ]
    ],
    //HttpServer的ip及端口配置
    'HttpServer'=>[
        'host'=>'0.0.0.0',
        'port'=>9012,
        //配置
        'set'=>[
            //必须包含
            'open_http_protocol' => true,
        ]
    ],
    //TcpServer的ip及端口配置
    'TCPServer'=>[
        'host'=>'0.0.0.0',
        'port'=>9013,
    ],
];