<?php


namespace app\controller;

use think\Collection;

class BaseController extends Collection
{
    public function _empty()
    {
        $res =[
            "code"=>"400",
            "msg"=>"访问页面不存在",
            "errorCode"=>"10001"
        ];
        return json($res,400);
    }
}