<?php


namespace app\api\controller;


use think\Config;

class Base
{
    function getBase(){
        allowCross();
        passOptions();
        $title = Config::get('web_title');
        $desc = Config::get('web_desc');
        $keywords = Config::get('web_keywords');

        return json(['errcode'=>0, 'message'=>'get success', 'title'=>$title, 'desc'=>$desc, 'keywords'=>$keywords]);

    }

}