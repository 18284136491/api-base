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

        config('_phoneCode', 300);// 短信验证码生存时间

    }

}