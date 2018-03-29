<?php
/**
 * Created by PhpStorm.
 * Author: Administrator
 * Date: 2018/3/20
 * Time: 10:34
 */

namespace app\index\validate;


class Member extends \think\Validate
{
    protected $rule = [
        'phone|手机号' => 'require|number|length:11',
        'nickname|昵称' => 'require|chsAlphaNum|length:2,25',
        'password|密码' => 'require|alphaNum|length:6,20',
        'repassword|确认密码' => 'confirm:password',
        'security_code|手机验证码' => 'require|number|length:6',
    ];

    protected $message = [
        'repassword.confirm' => '确认密码和密码不一致',
    ];

    protected $scene = [
        'signup' => ['phone', 'nickname', 'password', 'repassword', 'security_code'],
        'signin' => ['phone', 'password'],
        'send' => ['phone'],
    ];

}