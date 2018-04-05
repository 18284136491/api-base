<?php
/**
 * Created by PhpStorm.
 * Author: Administrator
 * Date: 2018/3/20
 * Time: 13:55
 */

namespace app\common\behavior;


class Config
{
    public function run()
    {
        config('_pwd', 'weixin520');// 密码加盐

        config('_phoneCodeExpiration', 300);// 短信验证码生存时间

        config('_tokenExpiration', 7200);// token生存时间

        config('_originalKey', md5('dears'));// 原始key

    }

}
