<?php
/**
 * 核心类库
 * User: minyifei.cn
 * Date: 17/2/22
 * Time: 下午8:10
 */
//设置时区
date_default_timezone_set('PRC');
//统一编码为utf8
mb_internal_encoding('UTF-8');
//开启session
session_start();
//系统路径
define('APP_SYS_PATH', dirname(__FILE__));
define('SYS_PATH', dirname(APP_SYS_PATH));
define('COMMON_PATH', SYS_PATH );
define('OP_CONF_DIR', '/data/config/selfbuild/');
//引用全局函数
require_once __DIR__ . "/functions.php";
//读取配置文件
$iniFiles = @dir_files(SYS_PATH . '/config');
$iniOpFiles = @dir_files(OP_CONF_DIR);
$iniFiles = array_merge($iniFiles,$iniOpFiles);
global $_gblConfig;
foreach ($iniFiles as $iniFile) {
    if(!isset($_gblConfig)){
        $_gblConfig=[];
    }
    $file = $iniFile['file'];
    $fileArr = explode("/",$file);
    $fileName = end($fileArr);
    $fileNames = explode(".",$fileName);
    $firstName = current($fileNames);
    $cs[$firstName] = include $file;
    $_gblConfig = array_merge($_gblConfig,$cs);
}

//加载命名空间文件
$_gblNamespaces = array(
    "Myf\Configs" => COMMON_PATH . "/configs",
    "Myf\Constants" => COMMON_PATH . "/constants",
    "Myf\Libs" => COMMON_PATH . "/libs",
    "Myf\Service" => COMMON_PATH . "/service",
    "Myf\Tests" => COMMON_PATH . "/tests",
    "Myf\Controller" => COMMON_PATH . "/controller",
    "Myf\Admin\Controller" => COMMON_PATH . "/admin/controller",
    "Myf\Model" => COMMON_PATH . "/model",
);
spl_autoload_register("loader");
//use composer autoloader to load vendor classes
include_once SYS_PATH . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
