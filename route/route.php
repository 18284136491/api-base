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

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::post('index', 'index/index');
Route::post('gettoken', 'token/getToken');// 获取token

Route::post('signup', 'login/signup');// 注册
Route::post('sendcode', 'login/sendcode');// 发送验证码
Route::post('signin', 'login/signin');// 登录



Route::post('memberinfo', 'member/getmemberinfo');// 获取用户信息
Route::post('signout', 'member/signout');// 退出登录

