<?php


namespace app\api\model;


use think\Model;

class Comment extends Model
{
    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }
}