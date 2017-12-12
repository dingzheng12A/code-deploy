<?php
/**
 * ioncube 不加密、需要删除的文件列表
 * User: myf
 * Date: 2017/11/7
 * Time: 16:01
 */
return [
    //需要忽略的文件
    'ignore'=>[
        'CloudDaemon/',
        '.DS_Store',
        '.git',
        '.git/',
        '.gitignore',
        '.idea',
        'Common/config/*/',
        'CloudWebSite/',
        'CloudDaemon/',
        'CloudREST/NovaRest/Tests/',
        'clearRuntime.sh',
        'CloudAdmin/Admin/Runtime/',
        'CloudLisence/Vnnox/Runtime/',
        'CloudOrder/Vnnox/Runtime/',
        'CloudInit/Vnnox/Runtime/',
        'CloudREST/NovaRest/Runtime/',
        'CloudSERVICE/NovaService/Runtime/',
        'CloudUpload/CloudUpload/Runtime/',
    ],
    //需要copy的文件
    'copy'=>[
        'Common/config/config.php',
        'GeoLite2-City.mmdb',
        'Common/ThinkPHP/',
        'CloudInit/SelfBuiltWeb',
        'CloudWEBAPP',
        'CloudWEBAPP/assets/js/common/kindeditor/php/JSON.php',
        'Common/ThinkPHP/Library/Org/Util/String.class.php',
        'Common/ThinkPHP/Library/ServiceLib/Lib/AdminTestCase.class.php',
        'Common/ThinkPHP/Library/ServiceLib/Lib/RestTestCase.class.php',
        'Common/ThinkPHP/Library/ServiceLib/Lib/VnnoxUtils.class.php',
    ],
    //需要加密的文件
    'encode'=>[
        'Common/ThinkPHP/Library/ServiceLib/',
        'Common/ThinkPHP/Library/Org/',
        'Common/ThinkPHP/Library/AdminLib/',
    ],
];