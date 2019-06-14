<?php


namespace app\api\controller;
use app\api\model\User as UserModel;

use think\Request;

class Profiles
{
    function resetPasswd(){
        allowCross();
        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $uid = Request::instance()->header('uid');
        $newPassWd = input('post.password');
        $salt = rand(100000, 999999);
        $resetPasswd = md5(md5($newPassWd) . $salt);

        $dbUser = new UserModel();
        $dbUser->save(['password'=> $resetPasswd,'salt'=>$salt], ['uid'=>$uid]);
        return json(['errcode'=>0, 'message'=>'reset password success']);
    }

    function resetAvatar(){
        allowCross();
        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }
        $uid = Request::instance()->header('uid');
        $avatar = request()->file('avatar');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($avatar){
            $info = $avatar->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $dbUser = new UserModel();
                $dbUser->save(['avatar'=> $info->getSaveName()], ['uid'=>$uid]);
                return json(['errcode'=>0, 'message'=>'upload avatar success']);
            }else{
                // 上传失败获取错误信息
                // echo $avatar->getError();
                 return json(['errcode'=>7, 'message'=>'upload avatar error']);
            }
        }
        return json(['errcode'=>8, 'message'=>'upload avatar error']);
    }

    function getAvatar(){
        header("Content-Type: image/jpeg");
        allowCross();
        $uid = input('get.uid');
        $dbUser = new UserModel();
        $userResult = $dbUser->where("uid = $uid")->find();
        $userAvatar = $userResult->avatar;
        $fileres = file_get_contents(ROOT_PATH . 'public' . DS .  "uploads" . DS . $userAvatar);

        echo $fileres;
        exit;


    }
}