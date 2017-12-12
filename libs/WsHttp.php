<?php
/**
 * remark
 * User: myf
 * Date: 2017/11/8
 * Time: 20:05
 */

namespace Myf\Libs;


class WsHttp
{

    /**
     * webSocket的http服务
     * @param string $uri 地址如：test/test
     * @param array $param
     * @return string
     */
    public static function get($uri,$param=[]){
        $host = config("vnnox.wsHttp");
        $url = sprintf("%s/%s",$host,$uri);
        return Http::get($url,$param);
    }

}