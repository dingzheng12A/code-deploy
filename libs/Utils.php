<?php
/**
 * 工具类
 * User: myf
 * Date: 17/2/23
 * Time: 上午6:55
 */

namespace Myf\Libs;


class Utils {

    /**
     * 读取一个请求唯一id
     * @return string
     */
    public static function getLogId($new = false) {
        $logId = session('log_id');
        if (empty($logId) || $new) {
            $randStr = uniqid(mt_rand(), true) . self::getMillisecond();
            $logId = md5($randStr);
            session('log_id', $logId);
        }
        return $logId;
    }

    /**
     * 读取当前毫秒数
     */
    static function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * 多维数组排序
     * @param $multi_array
     * @param $sort_key
     * @param int $sort
     * @return array|bool
     */
    public static function multiArraySort($multi_array, $sort_key, $sort = SORT_ASC) {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }


    /**
     * 请求入口记录日志函数
     */
    static function logRequestStart() {
        session('_start_time', Utils::getMillisecond());
        $msg = sprintf("request=【%s】", jsonCNEncode($_REQUEST));
        Log::write($msg);
    }

    /**
     * 请求出口记录日志函数
     * @param $response
     */
    static function logResponse($response = '') {
        $ct = Utils::getMillisecond() - session('_start_time');
        $msg = sprintf("ct=【%sms】,response=【%s】", $ct, jsonCNEncode($response));
        Log::write($msg);
    }

    /**
     * 获取两个标签之间的内容
     * @param $kw1
     * @param $mark1
     * @param $mark2
     * @return int|string
     */
    public static function getNeedBetween($kw1, $mark1, $mark2) {
        $kw = $kw1;
        $st = stripos($kw, $mark1);
        $ed = stripos($kw, $mark2);
        if (($st == false || $ed == false) || $st >= $ed)
            return 0;
        $kw = substr($kw, ($st + 1), ($ed - $st - 1));
        return $kw;
    }

    /**
     * curl请求获取内容
     * @param $url
     * @param string $charset
     * @return mixed|string
     */
    public static function curl($url, $charset = 'utf8') {
        $ch = curl_init();
        $randIp = rand(10, 99) . "." . rand(10, 99) . "." . rand(10, 99) . "." . rand(1, 99);
        $header = array(
            'CLIENT-IP:' . $randIp,
            'X-FORWARDED-FOR:' . $randIp,
        );
        $id = rand(1, 20000);
        //curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_SOCKS5);//使用了SOCKS5代理
        //curl_setopt($ch, CURLOPT_PROXY, "121.204.165.159:8118");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.{$id};)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $content = curl_exec($ch);
        curl_close($ch);
        if ($charset == 'gbk') {
            $content = mb_convert_encoding($content, "UTF-8", "GB2312");
        }
        return $content;
    }

   static function isMobileBrowser(){
        // returns true if one of the specified mobile browsers is detected
        // 如果监测到是指定的浏览器之一则返回true
        $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

        $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

        $regex_match.=")/i";

        // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
        return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

}