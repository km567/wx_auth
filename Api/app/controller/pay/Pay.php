<?php


namespace app\controller\pay;


use app\controller\auth\Token;
use app\model\User as UserModel;
use app\model\SysConfig as SysConfigModel;
use app\controller\pay\xcx\PayService;
use app\controller\pay\gzh\JsApi;

class Pay
{
    //公众号-我的订单页面中进行支付
    public function gzhPaySecond($id)
    {
        $order=UserModel::find(['id'=>$id]);
        $order_data['order_num']=$order['order_number'];
        $order_data['order_money']=$order['order_money'];
        $openid=Token::getCurrentTokenVar('openid');
        $gzh['web_name']=SysConfigModel::where(['key'=>'web_name'])->value('value');
        $gzh['api_url']=SysConfigModel::where(['key'=>'api_url'])->value('value');
        $res=(new JsApi())->gzh_pay($openid,$order_data,$gzh);
        return $res;
    }

    //获取调用小程序支付，必须的数据
    public function getPreOrder($id = '')
    {
        $pay = new PayService($id);
        return $pay->pay();
    }
}