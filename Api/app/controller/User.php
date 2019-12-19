<?php


namespace app\controller;

use app\controller\auth\Token;
use app\model\SysConfig;
use app\model\SysConfig as SysConfigModel;
use app\model\User as UserModel;
use app\lib\exception\BaseException;
use Aliyun\api_demo\SmsDemo;

class User extends BaseController
{
    //用户绑定手机
    public function bind_phone($phone,$code)
    {
        $uid=Token::getCurrentUid();
        $user = UserModel::where(['id' => $uid, 'code' => $code])->find();
        if (!$user) {
            throw new BaseException(['msg' => '验证码错误']);
        }
        if (time()>$user->code_time) {
            throw new BaseException(['msg' => '验证码已失效']);
        }
        //存在换手机号的漏洞
        $res=UserModel::where('id',$uid)->update(['mobile'=>$phone,'vip_status'=>1]);
        return $res?1:0;
    }

    //用户绑定手机时发送验证码
    public function send_mobile_code($mobile)
    {
        $code=rand(10000,99999);
        $uid=Token::getCurrentUid();
        $data['code']=$code;
        $data['code_time']=time()+300;
        $res=UserModel::where('id',$uid)->update($data);
        if(!$res){
            throw new BaseException(['msg'=>'发送失败']);
        }
        $setTemplateCode=SysConfigModel::where('key','setTemplateCode')->value('value');
        $setSignName=SysConfigModel::where('key','setSignName')->value('value');
        $res2 = SmsDemo::sendSms($mobile,$code,$setTemplateCode,$setSignName);
        return $res2?1:0;
    }
}