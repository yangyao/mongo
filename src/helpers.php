<?php
if ( ! function_exists('tool_path')) {
    function tool_path($path = '') {
        return app('path.tool').($path ? '/'.$path : $path);
    }
}

function mongoUniq() {
    return strval(new \MongoId());
}

function millisecond(){
    //	获取毫秒的时间戳
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval(TIME_NOW)) * 1000);
}

/*
 * @Notice: '容易冲突，只能使用在Admin后台等并发极少的地方'
 */
function simpleUniq() {
    $prefixArr = range('a', 'z');
    return uniqid($prefixArr[mt_rand(0, count($prefixArr)-1)]);
}

function json_decode_arr ($str) {
    if (strlen($str) == 0) {
        $res = array();
    }else{
        $res = json_decode($str, true);
    }
    return $res;
}

function json_decode_obj ($str) {
    if (strlen($str) == 0) {
        $res = new stdClass();
    }else{
        $res = json_decode($str);
    }
    return $res;
}

function makeBlockExt($ext) {
    if (empty($ext)) {
        return ['ext'=>new stdClass()];
    }
    return ['ext'=>$ext];
}

function message() {
    if (Session::has('message'))
    {
        $arr = explode('|', Session::get('message'));
        $type = array_shift($arr);
        $message = implode('|', $arr);
        $style = 'info';
        $style = $type == 'error' ? 'danger' : $style;
        //$type = $type == 'success' ? 'info' : '';
        return sprintf('<div class="alert alert-%s">%s</div>', $style, $message);
    }
    return '';
}

function pagination($itemTotal, $pageSize=false, $pageNum=1) {
    $ret = [];
    ($pageSize === false || $pageSize > $itemTotal) && $pageSize = $itemTotal;
    $pageNum = max(1, intval($pageNum));

    $ret['start'] = $pageSize * ($pageNum -1) + 1; //如果是使用数组的话，要-1
    $ret['end'] = min($itemTotal, $pageSize * $pageNum); //如果是使用数组的话，要-1
    $ret['size'] = $pageSize;
    $ret['num'] = $pageNum;
    $ret['total'] = $itemTotal == 0 ? 0 : ceil($itemTotal / $pageSize);
    return $ret;
}

function paginationReverse($itemTotal, $pageSize=false, $pageNum=1) {
    $ret = [];
    ($pageSize === false || $pageSize > $itemTotal) && $pageSize = $itemTotal;
    $pageNum = max(1, intval($pageNum));

    $ret['start'] = $itemTotal - ($pageSize * ($pageNum -1)); //如果是使用数组的话，要-1
    $ret['end'] = max(1, $itemTotal - ($pageSize * $pageNum) + 1); //如果是使用数组的话，要-1
    $ret['size'] = $pageSize;
    $ret['num'] = $pageNum;
    $ret['total'] = $itemTotal == 0 ? 0 : ceil($itemTotal / $pageSize);
    return $ret;
}

function makeExpireStr($expireTime) {
    if (date('H:i:s', $expireTime)=='00:00:00') {
        //return date('Y/m/d', $expireTime-1).' 24:00:00';
        return date('Y/m/d', $expireTime-1);
    }
    return date('Y/m/d H:i:s', $expireTime);
}

function makeDisplayPercent($a, $b) {
    $min = $a > 0 ? 1 : 0;
    $ret = max($min, floor($a / $b * 100));
    return (int)$ret;
}

function formatMoney($value, $withUnit=true) {
    //$value =  sprintf("%.2f", $value);
    $value = round($value/100, 2);
    if ($withUnit) {
        return $value. '元';
    }
    return $value;
}

//========================验证系列 start=========================
function isIntNum ($x) {
    return (is_numeric($x) ? intval($x) == $x : false);
}

function isNumber($x) {
    return is_numeric($x);
}
/*
 * @param $hourMinute="8:30:40"
 * @return 距离0点0分0秒的总秒数
 */
function getSecondsFromHms($hourMinuteSec) {
    //echo $hourMinuteSec;
    $hms = explode(':', $hourMinuteSec);
    if (count($hms) != 3) {
        return 0;
    }
    $ret = 3600 * $hms[0];
    $ret += 60 * $hms[1];
    $ret += $hms[2];
    return $ret;
}

function getHmsFromSecond($second) {
    $hour = floor($second / 3600);
    $min = floor(($second % 3600) / 60);
    $sec = $second % 3600 % 60;

    return sprintf("%02d", $hour) . ':' . sprintf("%02d", $min) . ':' . sprintf("%02d", $sec);
}
/*
 * $Hms = "09:06:30"
 */
function validAndHmsToSec($Hms) {
    $sec = getSecondsFromHms($Hms);
    if (getHmsFromSecond($sec) != $Hms) {
        throw new Exception_System(__METHOD__, '时间 格式错误：'.$Hms);
    }
    return $sec;
}
/*
 * $date = "2014-03-02 09:30:03" || "2014/03/02 09:30:03"
 */
function validAndDateToTimestamp($date) {
    $timestamp = strtotime($date);
    if (date("Y-m-d H:i:s", $timestamp) != $date && date("Y/m/d H:i:s", $timestamp) != $date) {
        throw new Exception_System(__METHOD__, '日期 格式错误：'.$date);
    }
    return $timestamp;
}

/*
 * $date = "20140202"
 */
function validSimpleDate($date) {
    $timestamp = strtotime($date);
    if (date("Ymd", $timestamp) != $date) {
        throw new Exception_System(__METHOD__, '简单日期 格式错误：'.$date);
    }
}

function trimOneInput($v) {
    if (is_int($v) || is_float($v) || is_string($v)) {
        return trim($v);
    }
    return $v;
}

function trimInputs() {
    Input::merge(array_map('trimOneInput', Input::all()));
}

/**
 * 获取客户端ip
 * @return string
 */
function getClientIp() {
    if(getenv('HTTP_CLIENT_IP')){
        $client_ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
        $client_ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR')) {
        $client_ip = getenv('REMOTE_ADDR');
    } else {
        $client_ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
    }
    return $client_ip;
}

/**
 * 获取客户端的UA字串
 * @return mixed
 */
function getUserAgent(){

    $agent = \Input::get('agent');
    if(!empty($agent)){
        return $agent;
    }
    if(isset($_SERVER['HTTP_USER_AGENT'])){
        return $_SERVER['HTTP_USER_AGENT'];
    }
    return '';
}
/**
 * 获取支付宝超时
 * @return mixed
 */
function getItbpay($timeout){
    return ($timeout/60).'m';
}
//========================验证系列 end=========================

/**
 * 二维数组排序
 * @return mixed
 */
function multiSort($arrays,$sortKey,$sortOrder=SORT_ASC,$sortType=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $keys[] = $array[$sortKey];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($keys,$sortOrder,$sortType,$arrays);
    return $arrays;
}

/**
 * 获取配置
 * @return mixed
 */

function config($key = null, $default = null){
    if (is_null($key)) {
        return app('config');
    }
    if (is_array($key)) {
        return app('config')->set($key);
    }
    return app('config')->get($key, $default);
}