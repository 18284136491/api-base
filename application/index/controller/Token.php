<?php

namespace app\index\controller;

class Token extends \think\Controller
{
    public function initialize()
    {
        $this->crossDomain();// 允许跨域
    }

    /**
     * getToken [获取token]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function getToken(\think\Request $request) : string
    {
        $param = $request->param();
        if(!$param['machine_model'] || !$param['access_type'] || !$param['key'] || !$param['sign']){
            $result = ['code' => 10031, 'msg' => '参数错误'];
            response($result);
        }

        // 签名数据
        $check_data = [
            'machine_model' => $param['machine_model'],
            'access_type' => $param['access_type'],
            'key' => md5('dears')
        ];

        // 签名验证
        if(!checkSign($check_data, $param['sign'])){
            $result = ['code' => 10032, 'msg' => '签名错误'];
            response($result);
        };

        // token生成
        $token = \Ramsey\Uuid\Uuid::uuid4()->toString();

        $key = uniqid();
        $data = [
            'uid' => 0,
            'machine_model' => $param['machine_model'],
            'access_type' => $param['access_type'],
            'expiration' => time() + 7200,
            'key' => $key,
            'ip' => $request->ip(),
        ];
        \Cache($token, json_encode($data));
        $result = [
            'token' => $token,
            'key' => $key
        ];
        return json_encode(['code' => 200, 'msg' => '操作成功', 'data' => $result]);
    }

    /**
     * crossDomain [允许跨域]
     *
     * author dear
     */
    private function crossDomain()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
        header('Access-Control-Allow-Headers:origin,x-requested-with,content-type');
        header('Access-Control-Max-Age:3600');
        header('X-Frame-Options:deny');
    }

}
