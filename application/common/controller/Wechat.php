<?php
/*
 * (c) U.E Dream Development Studio
 *
 * Author: 李益达 - Ekey.Lee <ekey.lee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\common\controller;

use think\Controller;
use think\Session;

class Wechat extends Controller
{
    const OAUTH_PERFIX = 'https://open.weixin.qq.com/connect/oauth2/authorize?'; //授权API
    const OAUTH_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?'; //获取access_token
    const OAUTH_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo?';
    //初始化 OAuth授权跳转
    public function _initialize()
    {
        $check = Session::has('openid');
        if ($check == null) {
            $code = input('get.code'); //获取网页参数
            $wechatObj = get_mpid_info();
            if (empty($code) || !isset($code)) {
                //todo 不存在code参数
                $OAuth_data = [
                    'appid' => $wechatObj['appid'],
                    'redirect_uri' => (thisUrl()),
                    'response_type' => 'code',
                    'scope' => 'snsapi_userinfo',
                    'state' => 1,
                ];
                $OAuth_data_build = (http_build_query($OAuth_data));
                $OAuth_request_url = self::OAUTH_PERFIX . $OAuth_data_build . '#wechat_redirect';
                $this->redirect($OAuth_request_url);
            } else {
                $this->getAuccessToken($code);
            }
        }

    }
    /**
     * 获取oauth access_token
     * @param string $code
     * @return array
     */
    public function getAuccessToken($code)
    {
        $wechatObj = get_mpid_info();
        $OAuth_data = [
            'appid' => $wechatObj['appid'],
            'secret' => $wechatObj['appsecret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $OAuth_data_build = http_build_query($OAuth_data);
        $OAuth_request_url = self::OAUTH_ACCESS_TOKEN . $OAuth_data_build;
        $reset = http_get($OAuth_request_url);
        $data = json_decode($reset, true);

        $this->checkCode($data);
        $this->OAuthUserinfo($data['access_token'],$data['openid']);
        Session::set('openid', $data['openid']);
        Session::set('access_token', $data['access_token']);
        Session::set('unionid', $data['unionid']);

    }
    /**
     * 判断code是否被使用
     * @param array result
     */
    public function checkCode($data)
    {
        if (!empty($data['errcode'])) {
            $this->redirect(thisUrl());
        }

    }
    /**
     * OAuth获取用户详细信息
     * @param string $access_token
     * @param string $openid
     * @author 李益达
     */
    public function OAuthUserinfo($access_token, $openid)
    {
        $build_data = [
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN',
        ];
        $result_build_data = http_build_query($build_data);
        $url = self::OAUTH_USER_INFO . $result_build_data;
        $result = http_get($url);
        $data = json_decode($result, true);
        $this->saveFansInfo($data);
    }
    /**
     * 保存粉丝信息到数据库
     * @param array $data 用户信息
     * @author 李益达
     */
    public function saveFansInfo($data)
    {
        $fansinfo = $this->emptyFansInfo($data);
        if ($fansinfo) {
            $data['mpid'] = get_mpid();
            db('fans_info')->data($data)->insert();
        } else {
            $update = [
                'headimgurl' => $data['headimgurl'],
                'nickname' => $data['nickname'],
                'sex' => $data['sex'],
                'language' => $data['language'],
                'city' => $data['city'],
                'province' => $data['province'],
                'country' => $data['country'],
            ];
            db('fans_info')->where(['openid' => $data['openid']])->data($update)->update();
        }
    }
    /**
     * 判断粉丝是否存在数据库
     * @param array 返回数据
     * @author 李益达
     */
    public function emptyFansInfo($data)
    {
        $fans = db('fans_info')->where(['openid' => $data['openid']])->find();
        if (empty($fans)) {
            return true;
        } else {
            return false;
        }
    }
}
