<?php
/**
 * 使用范例:
ExecCommand::execute(
'/usr/local/unoconv/bin/unoconv {f} {pdf} {filename}',
[
'f' => '-f',
'pdf'=>'pdf',
'filename'=>'/data/www/demo/c7c7c148880e518743acf368f9bbca4f[999-3].pptx'
]
);
ExecCommand::execute(
'/usr/bin/gs {dNumRenderingThreads} {sDEVICE} {output} {dstDir} {density} {pdfFile}',
[
'dNumRenderingThreads' => '-dNumRenderingThreads=4', //指定服务器的cpu核数
'sDEVICE' => '-sDEVICE=jpeg', //输出的图片的格式
'output' => '-o',//输出参数
'dstDir'=>$dstDir, //输出的文件名
'dJPEGQ'=>'-dJPEGQ=95', //图片质量
'density'=>'-r600x600', //像素的密度
'pdfFile'=>$pdfFile,//要转换的pdf文件
]
);
 */

namespace Myf\Libs;


class ExecCommand
{

    /**
     * 命令执行成功的标记 0表示成功
     */
    const EXECUTE_SUCCESS_CODE = 0;

    private function __construct(){
        //构造方法私有话防止外面实例化
    }

    /**
     * 执行命令
     * @param $command
     * @param array $params
     * @param bool $mergeStdErr
     * @return array
     */
    public static function execute($command, array $params = array(), $mergeStdErr=true){
        if (empty($command)) {
            throw new \InvalidArgumentException('命令不能为空');
        }

        $command = self::bindParams($command, $params);

        if ($mergeStdErr) {
            //将标准错误输出重定向到标准输出中去,方便出错时候排错
            $command .= ' 2>&1';
        }

        exec($command, $output, $code);

        if (self::EXECUTE_SUCCESS_CODE === $code) {
        } else {
            $output = implode(PHP_EOL, $output);
        }

        $res = [
            'output'=>$output,
            'code'=>$code,
        ];
        return $res;
    }

    /**
     * 绑定参数方法防止非法shell字符执行
     * @param $command
     * @param array $params
     * @return mixed
     */
    public static function bindParams($command, array $params){
        $wrappers = array();
        $converters = array();
        foreach ($params as $key => $value) {

            // 转码
            $wrappers[] = '{' . $key . '}';
            $converters[] = escapeshellarg(is_array($value) ? implode(' ', $value) : $value);

            // 解码
            $wrappers[] = '{!' . $key . '!}';
            $converters[] = is_array($value) ? implode(' ', $value) : $value;
        }

        return str_replace($wrappers, $converters, $command);
    }

}