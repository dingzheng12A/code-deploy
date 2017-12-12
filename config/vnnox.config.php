<?php
/**
 * vnnox配置
 * User: myf
 * Date: 2017/11/7
 * Time: 15:38
 */

return [
    //git地址
    'git'=>'git@172.16.80.102:vnnox/vnnox.git',
    //处理路径
    'path'=>'/data/ioncube',
    //ioncube的sh路径
    'ioncubeSh'=>'/data/ioncube_encoder_evaluation/ioncube_encoder.sh -54 ',
    //zip命令
    'zipSh'=>'zip -r ',
    //websocket
    'wsHost'=>'10.20.10.58:8911',
    'wsHttp'=>'http://10.20.10.58:8912',
    //dev上的服务地址
    'buildHttp'=>'http://10.20.10.58:8922',
    //系统模式
    'system'=>'linux',
    //发布目录
    'releasePath'=>'/data/selfBuildCode/release/',
    //更新目录
    'updatePath'=>'/data/selfBuildCode/update/',
];