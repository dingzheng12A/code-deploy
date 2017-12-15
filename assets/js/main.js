iziToast.settings({
    timeout: 3000,
    // position: 'center',
    // imageWidth: 50,
    pauseOnHover: true,
    // resetOnHover: true,
    close: true,
    progressBar: true,
    // layout: 1,
    // balloon: true,
    // target: '.target',
    // icon: 'material-icons',
    // iconText: 'face',
    // animateInside: false,
    // transitionIn: 'flipInX',
    // transitionOut: 'flipOutX',
});

SelfBuild = {};

SelfBuild.toastInfo = function (message) {
    iziToast.info({
        message: message,
        position: 'center',
        transitionIn: 'fadeIn'
    });
};

SelfBuild.toastError = function (message) {
    iziToast.error({
        message: message,
        position: 'center',
        transitionIn: 'fadeIn'
    });
};

SelfBuild.toastWarning = function (message) {
    iziToast.warning({
        message: message,
        position: 'center',
        transitionIn: 'fadeIn'
    });
};

SelfBuild.toastSuccess = function (message) {
    iziToast.success({
        message: message,
        position: 'center',
        transitionIn: 'fadeIn'
    });
};

SelfBuild.info = function (message) {
    var html = '<p class=\'bg-info wsItem\'>'+message+'</p>';
    $("#wsMessage").prepend(html);
};

SelfBuild.error = function (message) {
    var html = '<p class=\'bg-danger wsItem\'>'+message+'</p>';
    $("#wsMessage").prepend(html);
};

SelfBuild.warning = function (message) {
    var html = '<p class=\'bg-warning wsItem\' >'+message+'</p>';
    $("#wsMessage").prepend(html);
};

SelfBuild.success = function (message) {
    var html = '<p class=\'bg-success wsItem\'>'+message+'</p>';
    $("#wsMessage").prepend(html);
};

SelfBuild.html = function (html) {
    $("#wsMessage").html(html);
};

function initWs() {
    var wsHost = $("#hideWsHost").val();
    var wsServer = 'ws://'+wsHost+'/?c=auth&a=login&user=myf&pwd=myf&id=1';


    var websocket = new WebSocket(wsServer);

    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");

        var height = $(window).height();
        layer.open({
            id:'layerMessage',
            title:'系统消息',
            type: 1,
            offset: 'lt',
            area: ['300px', height+'px'],
            shade: 0,
            closeBtn:0,
            content: $("#wsMessage") //这里content是一个普通的String
        });

        websocket.onclose = function (evt) {
            console.log('Disconnected data from server: ' + evt.data);
            console.log("Disconnected");
            var error = "WebSocket连接关闭:"+evt.data;
            SelfBuild.error(error);
        };

        websocket.onmessage = function (evt) {
            console.log('Retrieved data from server: ' + evt.data);
            showWsMessage(evt.data);
        };
    };

    websocket.onerror = function (evt, e) {
        var error = 'WebSocket连接失败: ' + evt.data;
        console.log(error);
        SelfBuild.error(error);
    };
}

function sendMsg() {
    var text = document.getElementById("txtContent").value;
    var data = {
        id:rnd(10000000,99999999),
        param:{
            'name':"myf",
            'text':text,
        },
        c:"test",
        a:'swoole'
    };
    websocket.send(JSON.stringify(data));
}

function rnd(n, m){
    var random = Math.floor(Math.random()*(m-n+1)+n);
    return random;
}

function showWsMessage(data) {
    var obj = JSON.parse(data);
    var content = '';
    var msgType = 'info';
    if(typeof(obj.cmd)=='undefined'){
        var data = obj.data;
        content += '欢迎您:<br/>';
        content += 'userId:'+data.userId+'<br/>';
    }else{
        if(obj.cmd=='before'){
            content+='即将操作：'+obj.opt+"<br/>";
        }else{
            content += '完成操作：'+obj.opt+"<br/>";
            content += '执行命令：'+obj.command+"<br/>";
            content += '返回状态码：'+obj.code+"<br/>";
            if(obj.code!=0){
                content += '出错了：'+obj.output+"<br/>";
                msgType = 'error';
            }else{
                msgType = 'success';
            }
        }
    }

    if(typeof  obj.progress != 'undefined'){
        //修改进度条
        changeChildProgress(obj.progress);
    }

    switch (msgType){
        case 'info':
            SelfBuild.info(content);
            break;
        case 'error':
            SelfBuild.error(content);
            break;
        case 'success':
            SelfBuild.success(content);
            break
    }
}