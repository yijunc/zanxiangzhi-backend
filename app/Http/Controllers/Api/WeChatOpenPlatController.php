<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 10/8/18
 * Time: 7:29 PM
 */

namespace App\Http\Controllers\Api;


use App\Jobs\Tasks\WechatOpenPlatNotifyTask;
use App\Models\Admin;
use App\Models\Meta;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Http\Request;

class WeChatOpenPlatController
{
    public function process(Request $request)
    {
        $msg = $request->getContent();

        // parse xml from wechat server

        $obj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON);
        $openId = $dJSON->FromUserName;

        $task = new WechatOpenPlatNotifyTask(config("wechat.open_plat_maintain_template"),
            [
                'first' => [
                    'value' => '0000',
                    'color' => '#000000',
                ],
                'keyword1' => [
                    'value' => 'Need Maintenance',
                    'color' => '#000000',
                ],
                'keyword2' => [
                    'value' => date("Y-m-d h:i:sa"),
                    'color' => '#000000',
                ],
                'remark' => [
                    'value' => 'xxx',
                    'color' => '#000000',
                ]
            ]
        );
        Task::deliver($task);

        $admin = Admin::where('openid', $openId)->firstOrFail();
        if ($admin != null) {
            return '';
        }

        $admin = (new Admin());
        $admin->openid = $openId;
        $admin->save();

        return '';
    }

    public function firstTimeTokenReply(Request $request)
    {
//        var_dump($request);
        return $request->get("echostr");

    }
}