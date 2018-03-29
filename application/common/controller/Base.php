<?php

namespace app\common\controller;


class Base extends \think\Controller
{
    public function initialize()
    {
        $this->crossDomain();// 允许跨域
        $this->environmentAuth();// 环境验证
        $this->checkAuth();// 参数验证
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
     * checkAuth [token验证]
     *
     * author dear
     * @return array
     */
    private function checkAuth()
    {
        $param = $this->request->param();// 获取参数

        // token验证
        $this->checkToken($param);

        // 单点登录
        $this->singleSignOn($param['token']);
    }

    /**
     * checkToken [token验证]
     *
     * author dear
     * @param $data
     */
    private function checkToken($param)
    {
        $data = json_decode(\Cache::get($param['token']), true);

        // 回收过期token
        if($data['expiration'] <= time()){
            \Cache::rm($param['token']);// 回收token
            \Cache::rm($data['uid']);// 回收单点登录旧token
        }

        // 判断token值是否存在
        if(!$data){
            $result = ['code' => '1002', 'msg' => 'token值不存在'];
            response($result);
        }

        // 重复请求验证
        $request_count = \Cache::get($param['token'] . $param['random']);
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
        $data['expiration'] = time() + 7200;
        \Cache::set($param['token'], \GuzzleHttp\json_encode($data));
    }

    /**
     * singleSignOn [单点登录]
     *
     * author dear
     * @param $token
     */
    private function singleSignOn($token)
    {
        $data = json_decode(\Cache::get($token), true);
        echo $token."\n";
        echo \Cache::get($data['uid'])."\n\n";
        // 单点登录
        if($token != \Cache::get($data['uid'])){
            // 删除老token
            \Cache::rm(\Cache::get($data['uid']));
            // 删除单点登录key
            \Cache::rm($data['uid']);
        }
        echo $token."\n";
        echo \Cache::get($data['uid'])."\n\n";
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
