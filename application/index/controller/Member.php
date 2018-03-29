<?php
/**
 * Created by PhpStorm.
 * Author: Administrator
 * Date: 2018/3/29
 * Time: 16:07
 */

namespace app\index\controller;

use \app\common\controller\userBase;

class Member extends userBase
{

    public function _initialize()
    {
    }

    /**
     * getMemberInfo [获取用户信息]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function getMemberInfo(\think\Request $request) : string
    {
        $token = $request->param('token');
        $data = \GuzzleHttp\json_decode(\Cache::get($token), true);
        $map['id'] = $data['uid'];

        $memberinfo = \think\Db::name('member')->where($map)->find();
        if(!$memberinfo){
            $result = ['code' => 1, 'msg' => '用户不存在'];
            return \GuzzleHttp\json_encode($result);
        }

        $result = ['code' => 200, 'msg' => '操作成功', 'data' => $memberinfo];
        return \GuzzleHttp\json_encode($result);
    }

    /**
     * signOut [退出登录]
     *
     * author dear
     * @param \think\Request $request
     * @return string
     */
    public function signOut(\think\Request $request)
    {
        $token = $request->param('token');
        if(!\Cache::rm($token)){
            $result = ['code' => 1, 'msg' => '操作失败，请稍后再试'];
            return \GuzzleHttp\json_encode($result);
        }
        $result = ['code' => 0, 'msg' => '操作成功'];
        return \GuzzleHttp\json_encode($result);
    }

}
