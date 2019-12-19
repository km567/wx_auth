<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::post('auth/get_xcx_token', 'auth.Auth/getXcxToken');   //小程序获取用户token
Route::post('auth/token_verify', 'auth.Auth/verifyToken');   //验证用户token
Route::post('auth/upinfo', 'auth.Auth/xcx_upinfo');   //获取头像及昵称
Route::post('auth/decryptToGetMobile', 'auth.Auth/decryptToGetMobile');   //解密获取手机号
Route::post('order/pay/pre_order', 'pay.Pay/getPreOrder');//小程序支付
Route::post('order/pay/notify', 'pay.Pay/receiveNotify');//小程序支付回调

Route::get('user/send_mobile_code', 'User/send_mobile_code'); //发送验证码
Route::post('user/bind_phone', 'User/bind_phone');   //绑定手机号


Route::get('auth/wxcode_url', 'auth.Auth/wxcodeUrl');   //请求公众号code
Route::get('auth/gzh_token', 'auth.Auth/getToken');   //异步接收公众号code,获取openid，返回token
Route::post('order/second_pay', 'pay.Pay/gzhPaySecond');   //公众号第二次支付
Route::post('order/back/:ucid', 'pay.Pay/gzh_back'); //公众号支付回调