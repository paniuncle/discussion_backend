<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\api\model\User as UserModel;
use app\api\model\Session as SessionModel;
use think\Request;

// 应用公共文件
function allowCross(){
    header("Access-Control-Allow-Credentials:true");
    header("Access-Control-Allow-Origin: http://localhost:8080");//注意修改这里填写你的前端的域名
    header("Access-Control-Max-Age:3600");
    header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization,Uid");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
}
function passOptions(){
    $request = Request::instance()->method();
    if($request == "OPTIONS"){
        header("HTTP/1.1 204 No Content");
        die;
    }
}
function AuthenticateSession()
{
    $request = Request::instance()->method();
    if ($request != "OPTIONS") {
        $uid = Request::instance()->header('uid');
        $session = Request::instance()->header('authorization');
        $platform = Request::instance()->header('platform');

        $dbSession = new SessionModel();
        $resultSession = $dbSession->where("uid = $uid AND session = '$session' ")->find();
        if ($resultSession == null) {
            return false;
        }
        $timeout = $resultSession->timeout;
        $timeout = strtotime($timeout);
        if ($timeout < time()) {
            return false;
        }
        return true;
    } else {
        header("HTTP/1.1 204 No Content");
        die;
    }
}
function formatTime($time=''){
    $rtime = date("m-d H:i",$time);
    $htime = date("H:i",$time);
    $time = time() - $time;
    $str='';
    if ($time < 60){
        $str = '刚刚';
    }elseif($time < 60 * 60){
        $min = floor($time/60);
        $str = $min.'分钟前';
    }elseif($time < 60 * 60 * 24){
        $h = floor($time/(60*60));
        $str = $h.'小时前 ';
    }elseif($time < 60 * 60 * 24 * 3){
        $d = floor($time/(60*60*24));
        if($d==1){
            $str = '昨天 '.$htime;
        }else{
            $str = '前天 '.$htime;
        }
    }else{
        $str = $rtime;
    }
    return $str;

}

function AuthenticateGroup($needGroup,$uid){

    $dbUser = new UserModel();
    $dataUser = $dbUser->where("uid = $uid")->find();
    $group = $dataUser->group;
    if($group == $needGroup){
        return true;
    }else{
        return false;
    }
}