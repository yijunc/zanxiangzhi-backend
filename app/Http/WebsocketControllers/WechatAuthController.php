<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/15
 * Time: 3:38
 */

namespace App\Http\WebsocketControllers;


use App\Models\User;
use Junning\Swoole\Curl;

class WechatAuthController extends WsController
{
    public function login(){
        if(empty($this->data->code)){
            $this->fail("invalid code", 1);
            return;
        }
        $ret = json_decode($this->getOpenId($this->data->code));
        if(empty($ret) || empty($ret->openid)){
            $this->fail("auth not passed", 2);
            return;
        }
        $openid = $ret->openid;
        go(function() use ($openid){
            $ret = $this->updateToken($openid);
            $this->success(['uid' => $ret[0], 'api_token'=>$ret[1]]);
        });
    }

    private function getOpenId($code){
        return Curl::get("api.weixin.qq.com", 443, "api.weixin.qq.com", "/sns/jscode2session", [
            'appid' => config("wechat.app_id"),
            'secret' => config("wechat.app_secret"),
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ], true);
    }

    private function updateToken($openid){
        $apiToken = str_random(config('auth.api_token_length'));
        $user = (new User)->firstOrCreate(["wechat_openid" => $openid]);
        $user->api_token = $apiToken;
        $user->wechat_openid = $openid;
        $user->save();
        return [$user->id, $user->api_token];
    }

}