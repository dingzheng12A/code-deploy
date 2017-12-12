<?php
/**
 * 代码发布
 * User: myf
 * Date: 2017/12/12
 * Time: 11:44
 */

namespace Myf\Controller;


use Myf\Libs\CmsException;
use Myf\Libs\ExecCommand;
use Myf\Libs\MvcController;

class DeployController extends MvcController
{

    //命令所在目录
    private $shellPath;

    public function _before_action() {
        parent::_before_action();
        //初始化
        $this->shellPath = sprintf("%s/shell", SYS_PATH);
    }


    public function createAction() {
        $this->display();
    }

    //发布请求处理
    public function handlerAction() {
        set_time_limit(0);
        //描述
        $title = post("title");
        //待发布的tag号
        $tag = post("tag");
        //待发布的服务器节点
        $node = post("node");
        //step:1
        $this->fetchCode($tag);
        //step:2
        $this->jsCompress($tag);
        //step:3
        $this->codeCompressZip($tag);
        //step:4
        $this->codeZipTransfers($tag,$node);
        //step:5
        $this->codeUnzipRelease($tag,$node);
        //执行成功
        $res = [
            'ok'
        ];
        $this->echoSuccessJson($res);
    }


    /**
     * 1、代码拉取-tag拉取
     * @param string $tag vnnox待发布的tag号
     */
    private function fetchCode($tag) {
        $command = sprintf("%s/fetchCode.sh %s", $this->shellPath, $tag);
        $cmd = [
            'command' => $command,
            'opt' => sprintf('拉取代码:%s', $tag),
            'max'=>20,
        ];
        $this->exec($cmd);
    }

    /**
     * 2、js压缩-前端代码编译
     * @param string $tag vnnox待发布的tag号
     */
    private function jsCompress($tag) {
        $command = sprintf("%s/jsCompress.sh %s", $this->shellPath, $tag);
        $cmd = [
            'command' => $command,
            'opt' => sprintf('前端代码压缩编译:%s', $tag),
            'max'=>40,
        ];
        $this->exec($cmd);
    }

    /**
     * 3、代码压缩并生成md5文件
     * @param $tag
     */
    private function codeCompressZip($tag) {
        $command = sprintf("%s/codeCompressZip.sh %s", $this->shellPath, $tag);
        $cmd = [
            'command' => $command,
            'opt' => sprintf('代码压缩并生成md5文件:%s', $tag),
            'max'=>60,
        ];
        $this->exec($cmd);
    }

    /**
     * 4、代码包传输到目标服务器
     * @param $tag
     * @param $node
     */
    private function codeZipTransfers($tag, $node) {
        $command = sprintf("%s/codeZipTransfers.sh %s %s", $this->shellPath, $tag,$node);
        $cmd = [
            'command' => $command,
            'opt' => sprintf('代码包传输到目标服务器(ECS-%s):%s',$node, $tag),
            'max'=>80,
        ];
        $this->exec($cmd);
    }

    /**
     * 5、代码包解压并建立软连接
     * @param $tag
     * @param $node
     */
    private function codeUnzipRelease($tag, $node) {
        $command = sprintf("%s/codeUnzipRelease.sh %s %s", $this->shellPath, $tag,$node);
        $cmd = [
            'command' => $command,
            'opt' => sprintf('代码包解压并建立软连接(ECS-%s):%s',$node, $tag),
            'max'=>100,
        ];
        $this->exec($cmd);
    }

    /**
     * 执行命令
     * @param array $cmd 命令
     * @return mixed
     */
    private function exec($cmd) {
        $command = $cmd['command'];
        $beforeMsg = $cmd;
        $beforeMsg['cmd']='before';
        //进度条
        $beforeMsg['progress']=$cmd['max']-14;
        //发送操作前消息
        $this->pushText(json_encode($beforeMsg));
        //执行命令
        $res = ExecCommand::execute($command);
        //执行后发送消息
        $afterMsg = array_merge($res,$cmd);
        $afterMsg['cmd']='after';
        //进度条
        $afterMsg['progress']=$cmd['max'];
        $this->pushText(json_encode($afterMsg));

        $resCode = $res['code'];
        if ($resCode == 0) {
            return $res['output'];
        } else {
            //出现异常
            $error = $res['output'];
            CmsException::throwExp($error, $resCode);
        }
    }

}