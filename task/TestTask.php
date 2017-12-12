<?php
use Myf\Libs\ExecCommand;
use Myf\Libs\Task;

/**
 * remark
 * User: myf
 * Date: 2017/11/7
 * Time: 15:16
 */
class TestTask extends Task
{

    public function mainAction()
    {
        echo "task test\n";
    }

    public function testShellAction(){
        $shellDir = sprintf("%s/shell",SYS_PATH);
        $testSh = sprintf("%s/test.sh success",$shellDir);
        $res = ExecCommand::execute($testSh);
        print_r($res);
        $testSh = sprintf("%s/test.sh failure",$shellDir);
        $res = ExecCommand::execute($testSh);
        print_r($res);
    }

}