<?php
/**
 * myf swoole 框架
 * User: myf
 * Date: 2017/11/1
 * Time: 16:42
 */
define("APP_DIR", __DIR__);
require_once __DIR__ . '/bootstrap/core.php';
//webSocket服务配置
$wsConfig = config("swoole.WebSocket");
//http服务配置
$httpConfig = config('swoole.HttpServer');
//tcp服务配置
$tcpConfig = config("swoole.TCPServer");
//端口集合
$ports = [
    'ws'=> $wsConfig['port'],
    'hs'=> $httpConfig['port'],
    'tcp'=>$tcpConfig['port'],
];

//创建WebSocket服务器对象
$ws = new swoole_websocket_server($wsConfig['host'], $wsConfig['port']);
$wsSet = $wsConfig['set'];
if (!empty($wsSet)) {
    $ws->set($wsSet);
}

//监听WebSocket连接事件
$ws->on("open", function ($ws, $request) {
    $id = 0;
    $fd = $request->fd;
    $get = isset($request->get)?$request->get:[];
    plog(sprintf("WebSocket->open: fd=【%s】, request=【%s】", $request->fd, json_encode($get)));
    $refuse = true;
    if (!empty($get) && isset($get['c']) && isset($get['a'])) {
        $refuse = false;
        if (isset($get['id'])) {
            $id = $get['id'];
        }
        $data = [
            'c' => $get['c'], 'a' => $get['a'], 'param' => $get,
        ];
        initWebSocketMvc($data, $ws, $id, $fd, true);
    }

    if ($refuse) {
        $ws->close($fd);
    }
});


//监听WebSocket消息事件
$ws->on("message", function ($ws, $frame) {

    $fd = $frame->fd;
    $data = json_decode($frame->data, true);
    plog(sprintf("WebSocket->message: fd=【%s】,data=【%s】", $fd, $frame->data));
    $id = isset($data['id']) ? $data['id'] : 0;
    //接受到的数据
    $fail = true;
    if (isset($data) && isset($data['id']) && isset($data['c']) && isset($data['a'])) {
        $fail = false;
        //启动一个异步进程，处理事件
        $ws->after(1, function () use ($data, $ws, $id, $fd) {
            initWebSocketMvc($data, $ws, $id, $fd);
        });
    }

    //失败
    if ($fail) {
        $res = [
            'status' => 1, 'id' => $id,
        ];
        $ws->push($fd, json_encode($res));
    }
});

//监听WebSocket连接关闭事件
$ws->on("close", function ($ws, $fd, $reactorId) {
    plog(sprintf("WebSocket->close: fd=【%s】,reactorId=【%s】", $fd, $reactorId));
    if($reactorId>=0){
        $data = [
            'c' => 'auth', 'a' => 'quit','param'=>[],
        ];
        initWebSocketMvc($data, $ws, 0, $fd);
    }
});

//创建Http服务器对象
$hs = $ws->addlistener($httpConfig['host'], $httpConfig['port'], SWOOLE_SOCK_TCP);
$hsSet = $httpConfig['set'];
$hs->set($hsSet);

/*******处理监听Http请求********/
//监听http请求事件
$hs->on("request", function ($request, $response) use ($ws) {
    plog(sprintf("HttpServer->request: fd=【%s】,request=【%s】", $request->fd, json_encode($request)));
    $pathInfo = $request->server['path_info'];
    $s = trim(str_replace("/", " ", $pathInfo));
    $urls = explode(" ", $s);
    if (isset($urls[0])) {
        $c = $urls[0];
    }
    if (isset($urls[1])) {
        $a = $urls[1];
    }
    //默认访问方法为 index
    if (empty($a)) {
        $a = "index";
    }
    //默认控制器为 index
    if (empty($c)) {
        $c = "index";
    }
    $route = [
        'c' => $c, 'a' => $a,
    ];
    initHttpMvc($ws, $request, $response, $route);
});
$hs->on("close", function ($hs, $fd, $reactorId) {
    plog(sprintf("HttpServer->close: fd=【%s】,reactorId=【%s】", $fd, $reactorId));
});


/*******处理监听TCP请求********/
$ts = $ws->addlistener($tcpConfig['host'],$tcpConfig['port'],SWOOLE_SOCK_TCP);
$ts->set([]);
$ts->on('connect', function ($ts, $fd) {
    plog("ProxyClient:Connect {$fd}") ;
    $serverIp = getServerIp();
    $redis = \Myf\Libs\Redis::getInstance();
    global $ports;
    foreach ($ports as $port){
        $local = sprintf("%s-%s",$serverIp,$port);
        $redis->set($local,$fd);
        plog("proxy connect {$local}\n");
    }
});
$ts->on("receive",function ($ts, $fd, $from_id, $data) use($ws){
    $cmd = json_decode($data, true);
    echo "receive " . json_encode($cmd) . "\n";

    switch ($cmd['cmd']) {
        //消息转发
        case 'tran':
            //接受转发命令
            if (isset($cmd['remote']) && isset($cmd['msg'])) {
                plog(sprintf("接受到转发消息:remote=[%s],fd=[%s],msg=[%s]\n", $cmd['remote'],$fd['fd'], $cmd['msg']));
                $ws->push($cmd['fd'],$cmd['msg']);
            }
            break;
    }
});
$ts->on("close", function ($ts, $fd, $reactorId) {
    plog(sprintf("TCPServer->close: fd=【%s】,reactorId=【%s】", $fd, $reactorId));
    $serverIp = getServerIp();
    $redis = \Myf\Libs\Redis::getInstance();
    global $ports;
    foreach ($ports as $port){
        $local = sprintf("%s-%s",$serverIp,$port);
        $redis->del($local);
        plog("proxy close {$local}");
    }
});

//swoole启动成功事件
$ws->on("start", function ($ws) use ($wsConfig, $httpConfig,$tcpConfig) {
    $masterPid = $ws->master_pid;
    file_put_contents('/tmp/myf-swoole-master.pid', $masterPid);
    plog(sprintf("WebSocket=>host:%s,port=%s，master_pid=%s,started", $wsConfig['host'], $wsConfig['port'], $masterPid));
    plog(sprintf("HttpServer=>host:%s,port=%s,started", $httpConfig['host'], $httpConfig['port']));
    plog(sprintf("TCPServer=>host:%s,port=%s,started", $tcpConfig['host'], $tcpConfig['port']));

});
$ws->start();


function initTcpClient($wsConfig,&$ws,&$hs){
    /*********Client处理监听TCP请求************/
    //监听tcp客户端,用户集群消息转发
    $proxyConfig = config("swoole.ProxyServer");
    if(!empty($proxyConfig)){
        echo "proxy\n";
        $cli = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞
        $cli->on("connect", function($cli) use($wsConfig,$ws,$hs) {
            //签到
            $initData = [
                'cmd'=>'sign',
                'host'=>getServerIp(),
                'port'=>$wsConfig['port'],
            ];
            plog(sprintf("ClientTcp连接成功 [%s]",json_encode($initData)));
            $cli->send(json_encode($initData));
            //挂靠webSocket句柄对象上
            $ws->cli=$cli;
            $hs->cli=$cli;
        });

        $cli->on("receive", function($cli, $data) use($ws){
            echo sprintf("接受到消息转发请求:[%s]",$data);
            //实现消息转发
            $cmd = json_decode($data,true);
            if(isset($cmd['fd']) && isset($cmd['msg'])){
                $ws->push($cmd['fd'],$cmd['msg']);
            }
        });

        $cli->on("close", function($cli){
            echo "close\n";
        });

        $cli->on("error", function($cli){
            exit("error\n");
        });

        $cli->connect($proxyConfig['host'], $proxyConfig['port'], 0.5);
    }
}





/**
 * 初始化WebSocket控制器
 * @param array $data 数据
 * @param object $ws WebSocket句柄对象
 * @param String $id 请求标识
 * @param int $fd 通道id
 * @param bool $open 是否为WebSocket的open连接
 */
function initWebSocketMvc($data, $ws, $id, $fd, $open = false) {
    //控制器
    $className = ucfirst($data['c']) . 'Controller';
    //方法名
    $actionName = $data['a'] . 'Action';
    //参数
    $param = [
        'id' => $id, 'fd' => $fd, 'param' => $data['param'],
    ];

    $res = [
        'status' => 1, 'id' => $id,
    ];
    $sendMsg = true;
    //加载对应的类
    $fileName = SYS_PATH . "/controller/WebSocket/" . $className . ".php";
    if (file_exists($fileName)) {
        include_once($fileName);
        try {
            $sendMsg = false;
            $mvcControllerClass = sprintf("\Myf\Controller\WebSocket\%s", $className);
            $controller = new $mvcControllerClass;
            $controller->_sys_init_action($ws, $id);
            $controller->_before_action();
            $controller->$actionName($param);
            $controller->_after_action();
        } catch (\Exception $e) {
            $sendMsg = true;
            $res['status'] = $e->getCode();
            $res['error'] = $e->getMessage();
        }
    }

    if ($sendMsg) {
        $ws->push($fd, json_encode($res));
        //如果发生异常，初始化的连接需要断开
        if ($open) {
            $ws->close($fd);
        }
    }
}


/**
 * 初始化HttpServer的控制器
 * @param object $ws WebSocket句柄对象
 * @param object $request 请求对象
 * @param object $response 返回对象
 * @param array $route 路由
 */
function initHttpMvc($ws, $request, $response, $route) {
    //控制器
    $className = ucfirst($route['c']) . 'Controller';
    //方法名
    $actionName = $route['a'] . 'Action';
    //加载对应的类
    $fileName = SYS_PATH . "/controller/Http/" . $className . ".php";
    if (file_exists($fileName)) {
        include_once($fileName);
        try {
            $mvcControllerClass = sprintf("\Myf\Controller\Http\%s", $className);
            $controller = new $mvcControllerClass;
            $controller->_sys_init_action($ws, $request, $response);
            $controller->_before_action();
            $controller->$actionName();
            $controller->_after_action();
        } catch (\Exception $e) {
            $res['status'] = $e->getCode();
            $res['error'] = $e->getMessage();
            $response->status(500);
            $response->end("500");
        }
    } else {
        $response->status(404);
        $response->end("404");
    }
}


