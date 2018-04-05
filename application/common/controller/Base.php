<?php

namespace app\common\controller;

use \think\Cache;

class Base extends \think\Controller
{
    public function initialize()
    {
        $this->crossDomain();// 允许跨域
        $this->environmentAuth();// 环境验证
        $this->checkToken();// token验证
        $this->_initialize();// 初始化
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

    /**
     * environmentAuth [环境检查]
     *
     * author dear
     */
    private function environmentAuth()
    {
        $param = $this->request->param();
        // 参数验证
        if(!$param['key'] || !$param['platform'] || !$param['random'] || !$param['token'] || !$param['sign']){
            $result = ['code' => '1000', 'msg' => '非法的请求参数'];
            response($result);
        }

        // 检查redis是否启用
        try{
            \Cache::get($param['token']);
        } catch(\Exception $e){
            $result = ['code' => '1001', 'msg' => 'Cache未启用'];
            response($result);
        }
    }

    /**
     * checkToken [token验证]
     *
     * @author dear
     */
    private function checkToken()
    {
        $param = $this->request->param();// 获取参数
        $data = json_decode(\Cache::get($param['token']), true);

        // 判断token值是否存在
        if(!$data){
            $result = ['code' => '1002', 'msg' => 'token值不存在'];
            response($result);
        }

        // 重复请求验证
        $request_count = \Cache::remember($param['token'] . $param['random'], 0, 10);
        if($request_count < 4){
            \Cache::Inc($param['token'] . $param['random']);
        }else{
            \Cache::rm($param['token'] . $param['random']);
            $result = ['code' => '1003', 'msg' => '请不要重复请求'];
            response($result);
        }

        // 签名数据
        $check_arr = [
            'platform' => $param['platform'],
            'random' => $param['random'],
            'token' => $param['token'],
            'key' => $data['key']
        ];

        // 签名验证
        if(!checkSign($check_arr,$param['sign'])){
            $result = ['code' => 10032, 'msg' => '签名错误'];
            response($result);
        };

        // token生存时间
        \Cache::set($param['token'], json_encode($data), config('_tokenExpiration'));
    }

    /**
     * _initialize [初始化]
     *
     * author dear
     */
    protected function _initialize()
    {
    }

}
