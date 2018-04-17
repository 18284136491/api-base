<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * resopnse [抛出异常]
 *
 * author dear
 * @param $data
 * @param string $type
 */
function response($data, $type='json')
{
    $response = \think\Response::create($data, $type);
    throw new \think\Exception\HttpResponseException($response);
}

/**
 * checkSin [description]
 *
 * author dear
 * @param $check_data [要验证的数据]
 * @param $sign [原签名]
 */
function checkSign($check_data, $sign)
{
    ksort($check_data);
    $check_data = http_build_query($check_data);
    if(md5($check_data) !== $sign){
        return false;
    }
    return true;
}

/**
 * encryptPwd [密码加密]
 *
 * author dear
 * @param $pwd
 * @return string
 */
function encryptPwd(string $pwd) : string
{
    $res = strtoupper($pwd).config('_pwd');
    return md5($res);
}

/**
 * checkPwd [密码验证]
 *
 * author dear
 * @param $input_pwd
 * @param $pwd
 * @return bool
 */
function checkPwd(string $input_pwd, $pwd) : bool
{
    if(encryptPwd($input_pwd) !== $pwd){
        return false;
    }
    return true;
}

function signInCheck() : bool
{

}

/**
 * getUuid [生成uuid]
 *
 * author dear
 * @param $input_pwd
 * @param $pwd
 * @return bool
 */
function getUuid(string $prefix='') : string
{
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,10) . '-';
    $uuid .= substr($str,10,6) . '-';
    $uuid .= substr($str,16,6) . '-';
    $uuid .= substr($str,20);
    return strtoupper($prefix . $uuid);
}



/**
 * pregVerify [正则验证]
 *
 * author dear
 * @param $str : [要验证的字符串]
 * @param $type : [指定类型]
 * @return bool
 */
function pregVerify($str, $type){
    $patterns = array(
        'not_zn'	=>	'/[^\x{4e00}-\x{9fa5}]/u',		// 匹配非中文（UTF-8编码）
        'zn'		=>	'/^\x{4e00}-\x{9fa5}+$/u',		// 匹配中文（UTF-8编码）
        'not_int'	=>	'/[^\d]+?/',				// 匹配非整数数字
        'p_int'		=>	'/^\d+?$/',					// 匹配正整数
        'int'	   	=>  '/^[-\+]?\d+$/',			// 匹配整数
        'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',	// 匹配浮点数
        'phone'		=>	'/^1\d{10}$/',			// 匹配手机
        'email'		=>	'/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',	// 匹配邮箱
        'not_en'   	=>  '/[^A-Za-z]+?/',	// 匹配非英文
        'en'   		=>  '/^[A-Za-z]+$/',	// 匹配英文
        'bankcard'	=>	'/^(\d{16}|\d{18}|\d{19})$/',	// 银行卡号
        'blank'		=>	'/\s+?/s',		// 单行模式 匹配空白字符
        'common'	=>	'/[^0-9a-zA-Z]+?/', // 匹配非数字、字母、下划线
        'common_zn'	=>	'/[^0-9a-zA-Z_\x{4e00}-\x{9fa5}]+?/u',	// 匹配非数字、字母、下划线、中文（UTF-8编码）
        'url'       =>  "/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/"	// 匹配URL
    );
    if(!isset($patterns[$type])){
        return false;
    }
    return preg_match($patterns[$type], $str) === 1;
}


