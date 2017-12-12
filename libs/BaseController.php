<?php
/**
 * 基础类
 * User: myf
 * Date: 17/3/2
 * Time: 下午5:24
 */

namespace Myf\Libs;


class BaseController extends Controller{

    public function _before_action(){
        $user = $this->getCurrentUser();
        if(empty($user)){
            header("Location:" . getBaseURL() . "/login");
            exit;
        }
        $this->assign('currentUser',$user);
    }


    /**
     * 获取当前登录用户信息
     */
    public function getCurrentUser(){
        $user = session('CurrentUser');
        return $user;
    }

    /**
     * 获取当前登录用户信息的id
     * @return mixed
     */
    public function getCurrentUserId(){
        $user =$this->getCurrentUser();
        return $user['id'];
    }


    /**
     * https请求
     * @param $url
     * @return mixed
     */
    public function curlHttps($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    }

    // 两个日期之间的所有日期
    public function prDates($start, $end) {
        $dt_start = strtotime($start);
        $dt_end = strtotime($end);
        $days = [];
        while ($dt_start <= $dt_end) {
            $days[] = date('Y-m-d', $dt_start);
            $dt_start = strtotime('+1 day', $dt_start);
        }
        return $days;
    }

    /**
     * 获取指定日期之间的各个周
     * @param $sdate
     * @param $edate
     * @return array
     */
    public function get_weeks($sdate, $edate) {
        $range_arr = array();
        // 检查日期有效性
        $this->check_date(array( $sdate, $edate ));
        // 计算各个周的起始时间
        do {
            $weekinfo = $this->get_weekinfo_by_date($sdate);
            $end_day = $weekinfo['week_end_day'];
            $start = $weekinfo['week_start_day'];
            $end = $weekinfo['week_end_day'];
            $range = ['start'=>$start,'end'=>$end,'name'=>$this->substr_date($start).'~'.$this->substr_date($end)];
            $range_arr[] = $range;
            $sdate = date('Y-m-d', strtotime($sdate) + 7 * 86400);
        } while ($end_day < $edate);
        return $range_arr;
    }

    /**
     * 根据指定日期获取所在周的起始时间和结束时间
     */
    public function get_weekinfo_by_date($date) {
        $idx = strftime("%u", strtotime($date));
        $mon_idx = $idx - 1;
        $sun_idx = $idx - 7;
        return array(
            'week_start_day' => strftime('%Y-%m-%d', strtotime($date) - $mon_idx * 86400),
            'week_end_day' => strftime('%Y-%m-%d', strtotime($date) - $sun_idx * 86400),
        );
    }

    /**
     * 截取日期中的月份和日
     * @param string $date
     * @return string $date
     */
    public function substr_date($date) {
        if ( ! $date) return FALSE;
        return date('m-d', strtotime($date));
    }

    /**
     * 检查日期的有效性 YYYY-mm-dd
     * @param array $date_arr
     * @return boolean
     */
    public function check_date($date_arr) {
        $invalid_date_arr = array();
        foreach ($date_arr as $row) {
            $timestamp = strtotime($row);
            $standard = date('Y-m-d', $timestamp);
            if ($standard != $row) $invalid_date_arr[] = $row;
        }
        if ( ! empty($invalid_date_arr)) {
            die("invalid date -> ".print_r($invalid_date_arr, TRUE));
        }
    }

    /**
     * 获取某年第几周的开始日期和结束日期
     * @param int $year
     * @param int $week 第几周;
     */
    public function weekday($year,$week=1){
        $year_start = mktime(0,0,0,1,1,$year);
        $year_end = mktime(0,0,0,12,31,$year);

        // 判断第一天是否为第一周的开始
        if (intval(date('W',$year_start))===1){
            $start = $year_start;//把第一天做为第一周的开始
        }else{
            $week++;
            $start = strtotime('+1 monday',$year_start);//把第一个周一作为开始
        }

        // 第几周的开始时间
        if ($week===1){
            $weekday['start'] = $start;
        }else{
            $weekday['start'] = strtotime('+'.($week-0).' monday',$start);
        }

        // 第几周的结束时间
        $weekday['end'] = strtotime('+1 sunday',$weekday['start']);
        if (date('Y',$weekday['end'])!=$year){
            $weekday['end'] = $year_end;
        }
        $weekday['start']=date("Y-m-d",$weekday['start']);
        $weekday['end']=date("Y-m-d",$weekday['end']);
        return $weekday;
    }

}