<?php


namespace app\controller\pay\xcx;


use app\controller\auth\Token;
use app\model\SysConfig;
use app\model\User as UserModel;
use app\lib\exception\BaseException;
use think\Exception;

class PayService
{
    private $userID;
    private $orderNumber;

    function __construct($userID)
    {
        if (!$userID)
        {
            throw new Exception('订单号不允许为NULL');
        }
        $orderNumber=UserModel::where('id',$userID)->value('order_number');
        $this->userID = $userID;
        $this->orderNumber = $orderNumber;
    }

    //验证订单、支付、改变状态
    public function pay()
    {
        $order_money = UserModel::where('id',$this->userID)->value('order_money');//获取支付金额
        $order_money=100*$order_money;
        $result = $this->makeWxPreOrder($order_money);//进行微信支付
        return json($result);
    }

    //进行支付
    private function makeWxPreOrder($totalPrice)
    {
        $api_url=SysConfig::where(['key'=>'api_url'])->value('value');
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid)
        {
            throw new BaseException();
        }
        new WxPayData();
        $wxOrderData = new WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNumber);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice);
        $wxOrderData->SetBody('商城');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url($api_url."/order/pay/notify");
        $res = $this->getPaySignature($wxOrderData);

        return $res;
    }

    //获取预支付订单
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = WxPayApi::unifiedOrder($wxOrderData);    //获取预支付id:prepay_id
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        )

        {
            \think\facade\Log::record($wxOrder, 'error');
            \think\facade\Log::record('获取预支付订单失败', 'error');
        }
        //prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function recordPreOrder($wxOrder)
    {
        UserModel::where('id', $this->userID)
            ->update(['prepay_id' =>$wxOrder['prepay_id']]);
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new WxPayJsApiPay();
        $app_id = SysConfig::where('key','wx_app_id')->value('value');
        $jsApiPayData->SetAppid($app_id);
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['appId']);
        return $rawValues;
    }
}