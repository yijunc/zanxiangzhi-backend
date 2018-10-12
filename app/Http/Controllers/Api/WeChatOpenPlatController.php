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
use App\Models\Device;
use App\Models\Meta;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Http\Request;

class WeChatOpenPlatController
{
    public function processWeChatMessage(Request $request)
    {
        $msg = $request->getContent();

        // parse xml from wechat server

        $obj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON);
        $openId = $dJSON->FromUserName;
        $message = $dJSON->Content;
        $actions = explode(':', $message);
        if ($actions[0] == 'admin') {
            if(count($actions) != 2){
                return 'success';
            }
            Admin::firstOrCreate(['openid' => $openId]);
            $task = new WechatOpenPlatNotifyTask(config("wechat.open_plat_maintain_template"),
                [
                    'first' => [
                        'value' => '管理员请求通过',
                        'color' => '#FF0000',
                    ],
                    'keyword1' => [
                        'value' => $actions[1],
                        'color' => '#FF0000',
                    ],
                    'keyword2' => [
                        'value' => date("Y-m-d h:i:sa"),
                        'color' => '#000000',
                    ],
                    'remark' => [
                        'value' => $openId,
                        'color' => '#000000',
                    ]
                ]
            );
            Task::deliver($task);
        } else if ($actions[0] == 'reset') {
            if (count($actions) != 3) {
                return 'success';
            }
            $retMessage = '请求重置参数不正确（reset:id:amount)';
            $device = Device::find($actions[1]);
            if ($device != null && is_numeric($actions[2]) && $actions[2] <= 2500 && $actions[2] > 0) {
                $device->left_segment_count = $actions[2];
                $device->save();
                $retMessage = '设备' . $actions[1] . '被重置';
            }
            $task = new WechatOpenPlatNotifyTask(config("wechat.open_plat_maintain_template"),
                [
                    'first' => [
                        'value' => '请求重置',
                        'color' => '#FF0000',
                    ],
                    'keyword1' => [
                        'value' => $retMessage,
                        'color' => '#FF0000',
                    ],
                    'keyword2' => [
                        'value' => date("Y-m-d h:i:sa"),
                        'color' => '#000000',
                    ],
                    'remark' => [
                        'value' => $openId,
                        'color' => '#000000',
                    ]
                ]
            );
            Task::deliver($task);
        }
        return 'success';
    }

    public function firstTimeTokenReply(Request $request)
    {
//        var_dump($request);
        return $request->get("echostr");

    }
}