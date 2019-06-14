<?php

namespace app\api\controller;
use app\api\model\User as UserModel;
use app\api\model\Session as SessionModel;
use app\api\model\User;

$root = dirname($_SERVER['DOCUMENT_ROOT']);

include "$root/application/common/api/authenticate/function.php";



class Authenticate
{
    function signIn(){
        allowCross();
        $username = input("post.username");
        $password = input("post.password");
        if($username == null || $password == null){
            return authenticateFailedNull();
        }

        // 验证用户信息部分
        $dbUser = new UserModel();
        $checkResult = $dbUser->where("username = '$username'")->find();
        if($checkResult == null){
            return notFoundUser();
        }

        $salt = $checkResult->salt;
        $dbPasswd = $checkResult->password;
        $userID = $checkResult->uid;

        $md5Passwd = md5(md5($password) . $salt);

        // 分发session部分
        $dbSession = new SessionModel();
        $sessionCheck = $dbSession->where("uid = $userID")->find();
        $session = md5($md5Passwd . rand(100000,999999) . time());
        if($sessionCheck == null){
            $dbSession->uid = $userID;
            $dbSession->session = $session;
            $dbSession->timeout = date('Y-m-d H:i:s',time() + (60 * 60 * 24));
            $dbSession->save();
        }else{
            $dbUpdateSession = new SessionModel();
            $dbUpdateSession->save(['session'=>$session,'timeout'=>date('Y-m-d H:i:s',time() + (60 * 60 * 24))],
                ['uid'=> $userID]);
        }


        if($dbPasswd == $md5Passwd){
            return json(['errcode' => 0, 'message' => 'Success Sign In', 'session' => $session, 'uid' => $userID, 'username' => $username]);
        }else{
            return passwordNotSame();
        }


    }

    function signUp(){
        allowCross();

        $username = input("post.username");
        $password = input("post.password");
        if($username == null || $password == null){
            return authenticateFailedNull();
        }

        // 首先判断是否注册过这个用户名
        $dbUserCheck = new UserModel();
        $checkResult = $dbUserCheck->where("username = '$username'")->find();
        if($checkResult != null){
            return haveUser();
        }

        $salt = rand(100000,999999);

        $dbUser = new UserModel();
        $dbUser->username = $username;
        $dbUser->password = md5(md5($password).$salt);
        $dbUser->salt = $salt;
        $dbUser->reg_time = date('Y-m-d H:i:s',time());
        $dbUser->status = 0;
        $dbUser->save();


        return json(['errcode'=>0,'msg'=>'sign up success', 'data'=>['id'=>$dbUser->uid , 'username'=> $username]]);




    }

    function getUserGroup(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $uid = input("post.uid");
        $dbUser = new UserModel();
        $dataUser = $dbUser->where("uid = $uid")->find();
        return json(['errcode'=>0, 'message'=>'get user group success', 'group'=> $dataUser->group]);

    }
}