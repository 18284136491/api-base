<?php
/**
 * Created by PhpStorm.
 * Author: Administrator
 * Date: 2018/3/19
 * Time: 18:41
 */

namespace app\common\controller;


class userBase extends Base
{
    public function _initialize()
    {
        $this->checkLogin();// 登录验证
        $this->_init(); // 初始化
    }

    /**
     * checkLogin [登录验证]
     *
     * author dear
     */
    private function checkLogin()
    {
        $param = $this->request->param();
        $data = json_decode(\Cache::get($param['token']), true);

        // 登录验证
        if(!$data['uid']){
            $result = ['code' => 1, 'msg' => '您还没有登录，请先登录'];
            response($result);
        }

        // 单点登录生存时间
        \Cache::set($param['uid'], json_encode($data), config('_tokenExpiration'));
    }

    protected function _init()
    {
    }

}
