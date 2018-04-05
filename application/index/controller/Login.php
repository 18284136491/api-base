<?php
/**
 * Created by PhpStorm.
 * Author: Administrator
 * Date: 2018/3/19
 * Time: 18:58
 */

namespace app\index\controller;


class Login extends \app\common\controller\Base
{
    public function _initialize()
    {
    }

    /**
     * signUp [注册]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function signUp(\think\Request $request)
    {
        $param = $request->param();

        // 参数验证
        $validate = validate('Member');
        $vali_res = $validate->scene('signup')->check($param);
        if(!$vali_res){
            return json_encode(['code' => 1, 'msg' => $validate->getError()]);
        }

        // 用户验证
        $member = \think\Db::name('member')->where('phone', $param['phone'])->find();
        if($member){
            return json_encode(['code' => 2, 'msg' => '手机号已经被占用']);
        }

        // 验证手机验证码
        $phone_res = $this->securityCode($param['phone'], $param['security_code']);
        if($phone_res){
            return $phone_res;
        }

        // 注册
        $register_data = [
            'phone' => $param['phone'],
            'username' => $param['nickname'],
            'password' => encryptPwd($param['password']),
        ];
        $register = \think\Db::name('member')->insert($register_data);
        if(!$register){
            return json_encode(['code' => '3', 'msg' => '操作失败，请稍后再试']);
        }
        return json_encode(['code' => 200, 'msg' => '操作成功']);
    }

    /**
     * signIn [登录]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function signIn(\think\Request $request)
    {
        $param = $request->param();
        $data = json_decode(\Cache::get($param['token']), true);

        // 登录验证
        if($data['uid']){
            $result = ['code' => 1, 'msg' => '您已经登录了，请不要重复登录'];
            response($result);
        }

        // 参数验证
        $validate = validate('Member');
        $res = $validate->scene('signin')->check($param);
        if(!$res){
            return json_encode(['code' => 1, 'msg' => $validate->getError()]);
        }

        // 用户验证
        $member = \think\Db::name('member')->where('phone', $param['phone'])->find();
        if(!$member){
            return json_encode(['code' => 2, 'msg' => '用户不存在']);
        }

        // 密码验证
        $checkpwd = checkPwd($param['password'], $member['password']);
        if(!$checkpwd){
            return json_encode(['code' => 3, 'msg' => '密码错误']);
        }

        // 单点登录
        $this->singleSignOn($member['id'], $param['token']);

        // 保存登录信息
        $data['uid'] = $member['id'];
        \Cache::set($param['token'], json_encode($data), config('_tokenExpiration'));// cache重新赋值
        \Cache::set($data['uid'], $param['token'], config('_tokenExpiration'));// 保存登录的token
        return json_encode(['code' => 200, 'msg' => '操作成功']);
    }

    /**
     * sendCode [发送验证码]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function sendCode(\think\Request $request)
    {
        $param['phone'] = $request->param('phone');

        // 参数验证
        $validate = validate('Member');
        $vali_res = $validate->scene('send')->check($param);
        if(!$vali_res){
            return json_encode(['code' => 1, 'msg' => $validate->getError()]);
        }

        $code = mt_rand(100000,999999);
        // 发送验证码
        \Cache::set($param['phone'], $code, config('_phoneCodeExpiration'));
        return json_encode(['code' => 200, 'msg' => '操作成功']);
//        return json_encode(['code' => 200, 'msg' => '操作成功', 'data' => ['code' => $code]]);
    }

    /**
     * securityCode [验证手机验证码]
     *
     * author dear
     * @param $phone
     * @param $code
     * @return string
     */
    private function securityCode($phone, $code)
    {
        $cache_code = \Cache::get($phone);
        if(!$cache_code){
            return json_encode(['code' => 2, 'msg' => '验证码过期或已超时，请从新发送']);
        }
        if($cache_code !== $code){
            return json_encode(['code' => 3, 'msg' => '验证码错误']);
        }
        \Cache::rm($phone);
    }

    /**
     * singleSignOn [单点登录]
     *
     * author dear
     * @param $token
     */
    private function singleSignOn($uid, $token)
    {
        $old_token = \Cache::get($uid);
        // 单点登录
        if($token != $old_token){
            // 删除老token
            \Cache::rm($old_token);
            // 删除单点登录key
            \Cache::rm($uid);
        }
    }

}
