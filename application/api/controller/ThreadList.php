<?php


namespace app\api\controller;
use app\api\model\Thread as ThreadModel;


class ThreadList
{
    function getList(){

        allowCross();
        $page = input("post.page");
        $div = input("post.div");
        $dbPage = new ThreadModel();
        $threadNum = $dbPage->where("status = 0")->count();
        $maxPage = ceil($threadNum / 20);
        if($page > $maxPage){
            return json(['errcode'=>5, 'message'=>'page max']);
        }

        if($div == 0){
            $dbThreadList = new ThreadModel();
            $threadList = $dbThreadList->where("status = 0")->order('active_time','DESC')->page($page, 20)->select();
            if($threadList == null){
                return json(['errcode'=>6, 'message'=>'no thread']);
            }
            //格式化时间
            for($i=0;$i<count($threadList);$i++){
                $threadList[$i]['active_time'] = formatTime(strtotime($threadList[$i]['active_time']));
            }

            return json(['errcode'=>0, 'message'=>'select success', 'thread'=>$threadList]);
        }else{
            $dbThreadList = new ThreadModel();
            $threadList = $dbThreadList->where("status = 0 AND division = $div")->order('active_time','DESC')->page($page, 20)->select();
            if($threadList == null){
                return json(['errcode'=>6, 'message'=>'no thread']);
            }
            //格式化时间
            for($i=0;$i<count($threadList);$i++){
                $threadList[$i]['active_time'] = formatTime(strtotime($threadList[$i]['active_time']));
            }

            return json(['errcode'=>0, 'message'=>'select success', 'thread'=>$threadList]);
        }



    }
}