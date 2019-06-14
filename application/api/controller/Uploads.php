<?php


namespace app\api\controller;


class Uploads
{
    function uploads(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $file = request()->file('image');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                return json(['errcode'=>0, 'message'=>'uploads success', 'url'=>'http://' .$_SERVER['HTTP_HOST']. DS . 'uploads' . DS .$info->getSaveName()]);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

}