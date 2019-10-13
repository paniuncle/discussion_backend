<?php


namespace app\api\controller;
use app\api\model\Thread as ThreadModel;
use app\api\model\ThreadContent as ThreadContentModel;

use app\api\model\Comment as CommentModel;
use app\api\model\CommentContent as CommentContentModel;
use app\api\model\User as UserModel;
use think\Request;

class Thread
{
    function getThread(){
        allowCross();
        passOptions();
        $tid = input("post.tid");
        $dbThread = new ThreadModel();
        $dbThreadContent = new ThreadContentModel();

        $dataBase = $dbThread->where("tid = $tid")->find();
        if($dataBase->status !== 0){
            return json(['errcode'=> 9, 'message'=> 'Thread status not formal']);
        }
        $dataContent = $dbThreadContent->where("tid = $tid")->find();

        $dbThread = new \app\api\model\Thread();

        $dbThread->where("tid = $tid")->setInc('view',1);


        return json(['errcode'=> 0, 'message'=> 'everything is ok', 'thread'=>['base'=>$dataBase,'content'=>$dataContent]]);

    }

    function replyThread(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $tid = input("post.tid");
        $content = input("post.content");
        $uid = Request::instance()->header('uid');

        // TODO 增加回复评论，更新主题的活跃时间

        $dbComment = new CommentModel();
        $dbCommentContent = new CommentContentModel();

        $dbComment->tid = $tid;
        $dbComment->uid = $uid;
        $dbComment->create_time = date("Y-m-d H:i:s", time());
        $dbComment->status = 0;
        $dbComment->save();
        $cid = $dbComment->cid;

        $dbCommentContent->cid = $cid;
        $dbCommentContent->content = $content;
        $dbCommentContent->save();

        $dbThread = new \app\api\model\Thread();
        $dbThread->where("tid = $tid")->setInc('reply',1);

        return json(['errcode'=>0, 'message'=>'create comment success']);
    }

    function getReplyList(){
        allowCross();
        passOptions();
        $tid = input("post.tid");
        $page = input("post.page");

        // 计算总页数的
        $dbCommentPage = new CommentModel();
        $commentNum = $dbCommentPage->where("status = 0")->count();
        $maxPage = ceil($commentNum / 20);
        if($page > $maxPage){
            return json(['errcode'=>5, 'message'=>'page max']);
        }

        // 拉取评论列表
        $dbComment = new CommentModel();
        $dbCommentContent = new CommentContentModel();
        $dbUser = new UserModel();

        $dataComment = $dbComment->where("tid = $tid AND status = 0")->order('cid','desc')->page($page,20)->select();

        // 获取评论内容
        for($i=0;$i<count($dataComment);$i++){
            $dataCommentContent = $dbCommentContent->where("cid = " . $dataComment[$i]['cid'])->find();
            $dataComment[$i]['content'] = $dataCommentContent->content;
        }

        // 获取用户名
        for($i=0;$i<count($dataComment);$i++){
            $dataUser = $dbUser->where("uid = " . $dataComment[$i]['uid'])->find();
            $dataComment[$i]['username'] = $dataUser->username;
        }


        return json(['errcode'=>0, 'message'=>'select comment success', 'comment'=> $dataComment]);

    }

    function delReply(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        $authG2 = AuthenticateGroup(3,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false && $authG2 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $cid = input("post.cid");
        $dbComment = new CommentModel();
        $dbComment->save(['status'=> 1], ['cid'=>$cid]);
        $tid = $dbComment->tid;

        $dbThread = new \app\api\model\Thread();
        $dbThread->where("tid = $tid")->setDec('reply',1);

        return json(['errcode'=>0, 'message'=>'delete reply success']);

    }

    function delThread(){
        allowCross();
        passOptions();
        $authR = AuthenticateSession();
        $authG1 = AuthenticateGroup(2,Request::instance()->header('uid'));
        $authG2 = AuthenticateGroup(3,Request::instance()->header('uid'));
        if($authR == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }else if($authG1 == false && $authG2 == false){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $tid = input("post.tid");
        $dbThread = new ThreadModel();
        $dbThread->save(['status'=>1], ['tid'=>$tid]);
        return json(['errcode'=>0, 'message'=>'delete Thread success']);

    }
}