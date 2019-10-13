<?php


namespace app\api\controller;
use app\api\model\User as UserModel;
use app\api\model\Thread as ThreadModel;
use app\api\model\UserGroup as UserGroupModel;
use app\api\model\Division as DivisionModel;
use think\Config;
use think\Request;

class AdminBoard
{
    function getBaseNum(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $dbUser = new UserModel();
        $userNum = $dbUser->count();
        $dbThread = new ThreadModel();
        $threadNum = $dbThread->count();
        return json(['errcode'=>0, 'message'=> 'get number success', 'usernum'=> $userNum, 'threadnum'=> $threadNum]);
    }

    function getUserList(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $page = input("post.page");
        $dbCount = new UserModel();
        $userNum = $dbCount->count();
        $maxPage = ceil($userNum / 20);
        if($page > $maxPage){
            return json(['errcode'=>5, 'message'=>'page max']);
        }

        // 接受条件搜索
        $uid = input("post.uid");
        $username = input("post.username");
        $email = input("post.email");
        $group = input("post.group");

        $sql = null;

        if($uid != null){
            if($sql == null){
                $sql .= "uid = " . $uid;
            }else{
                $sql .= " AND uid = " . $uid;
            }
        }
        if($username != null){
            if($sql == null){
                $sql .= "username LIKE \"" . $username . "\"";
            }else{
                $sql .= " AND username LIKE \"" . $username . "\"";
            }
        }
        if($email != null){
            if($sql == null){
                $sql .= "email = \"" . $email . "\"";
            }else{
                $sql .= " AND email = \"" . $email . "\"";
            }
        }
        if($group != null){
            if($sql == null){
                $sql .= "`group` = " . $group;
            }else{
                $sql .= " AND `group` = " . $group;
            }
        }
        // TODO 分页
        $dbUser = new UserModel();
        $dataUser = $dbUser->where($sql)->select();
        return json(['errcode'=>0, 'message'=>'select success', 'userlist'=> $dataUser, 'group'=> $group]);
    }

    function getUserGroup(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $dbUserGroup = new UserGroupModel();
        $dataGroup = $dbUserGroup->select();

        return json(['errcode'=>0, 'message'=> 'select group success', 'group'=>$dataGroup]);
    }

    function banUser(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $uid = input("post.uid");

        $dbUser = new UserModel();
        $dbUser->save(['status'=>1],['uid'=>$uid]);
        return json(['errcode'=>0, 'message'=>'ban user success']);
    }

    function unBanUser(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $uid = input("post.uid");

        $dbUser = new UserModel();
        $dbUser->save(['status'=>0],['uid'=>$uid]);
        return json(['errcode'=>0, 'message'=>'Unban user success']);
    }

    function changeUser(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $uid = input("post.uid");
        $username = input("post.username");
        $password = input("post.password");
        $group = input("post.group");
        $email = input("post.email");
        if($password != null){

            $salt = rand(100000, 999999);
            $password = md5(md5($password).$salt);
            $dbUser = new UserModel();
            $dbUser->save(['username'=>$username,'password'=>$password,'salt'=>$salt,'email'=>$email,'group'=>$group],['uid'=>$uid]);
        }else{
            $dbUser = new UserModel();
            $dbUser->save(['username'=>$username,'email'=>$email,'group'=>$group],['uid'=>$uid]);
        }

        return json(['errcode' => 0, "message"=>"change the user success"]);



    }

    function setWebsite(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }
        $title = input("post.title");
        $desc = input("post.desc");
        $keywords = input("post.keywords");


        echo config('web_title',$title);
        //Config::set(['web_title'=>$title]);
        config('web_desc', $desc);
        config("web_keywords", $keywords);
        return json(['errcode'=>0, 'message'=>'set success']);
    }

    function addDiv(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }
        $div_name = input("div_name");
        $div_desc = input("div_desc");

        $db_div = new DivisionModel();
        $db_div->name = $div_name;
        $db_div->desc = $div_desc;
        $db_div->save();
        return json(['errcode'=>0, 'msg'=>'ok']);

    }

    function delDiv(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $div_id = input("div_id");

        $db_div = new DivisionModel();
        $db_div->save(['status'=>1],['did'=>$div_id]);
        return json(['errcode'=>0, 'msg'=>'ok']);
    }



}