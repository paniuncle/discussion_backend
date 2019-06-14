<?php


namespace app\api\controller;


use app\api\model\Thread as ThreadModel;
use app\api\model\ThreadContent as ThreadContentModel;

use think\Request;

class PostNewThread
{
    function doPost(){
        allowCross();

        $authR = AuthenticateSession();
        if($authR != true){
            return json(['errcode'=>1001, 'message'=>'Authentication Failure']);
        }

        $title = input('post.title');
        $content = input('post.content');
        $div = input('post.div');

        // TODO 对传入的数据进行分析，是否为空的判断

        $dbThread = new ThreadModel();
        $dbThread->title = $title;
        $dbThread->division = $div;
        $dbThread->view = 0;
        $dbThread->reply = 0;
        $dbThread->uid = Request::instance()->header('uid');
        $dbThread->active_time = date('Y-m-d H:i:s', time());
        $dbThread->save();

        $tid = $dbThread->tid;
        $dbThreadContent = new ThreadContentModel();
        $dbThreadContent->tid = $tid;
        $dbThreadContent->content = $content;
        $dbThreadContent->save();

        return json(['errcode'=>0, 'message'=>'Post success', 'tid'=> $tid]);




    }

}