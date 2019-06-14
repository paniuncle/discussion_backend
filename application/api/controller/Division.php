<?php


namespace app\api\controller;
use app\api\model\Division as DivisionModel;


class Division
{
    function getDivisionList(){
        allowCross();
        passOptions();
        $dbDivision = new DivisionModel();
        $dataDiv = $dbDivision->where("status = 0")->select();

        return json(['errcode'=>0, 'message'=>'get division list success', 'division'=>$dataDiv]);


    }
    function getDivision(){
        allowCross();
        passOptions();
        $did = input("post.did");
        $dbDivision = new DivisionModel();
        $dataDiv = $dbDivision->where("did = $did")->find();

        return json(['errcode'=>0, 'message'=>'get division success', 'division'=> $dataDiv]);


    }

}