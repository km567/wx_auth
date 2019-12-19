<?php


namespace app\controller\pay\gzh;


use app\controller\auth\Token;
use think\Exception;

class JsApi
{
    public static function gzh_pay($openid,$order_data,$gzh)
    {
        $jsApiParameters="";
        try{
            $tools = new JsApiPay();
            new WxPayDataBase();
            $input = new WxPayUnifiedOrder();
            $input->SetBody($gzh['web_name']);
            $input->SetAttach("test");
            $input->SetOut_trade_no($order_data['order_num']);
            $input->SetTotal_fee($order_data['order_money']);
            $input->SetTime_start(date("YmdHis"));
            //$input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $uid=Token::getCurrentTokenVar('uid');
            $input->SetNotify_url($gzh['api_url']."/index.php/v1/back/".$uid);
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openid);
            $config = new WxPayConfig();
            $order = WxPayApi::unifiedOrder($config, $input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
        } catch(Exception $e) {

        }
        return $jsApiParameters;
    }
}