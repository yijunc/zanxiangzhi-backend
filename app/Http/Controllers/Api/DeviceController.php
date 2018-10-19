<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/17
 * Time: 22:23
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Tasks\WechatOpenPlatNotifyTask;
use App\Models\Device;
use App\Jobs\Tasks\DeviceActivator;
use App\Models\Report;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getStatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = $request->input("id");
        $device = $this->getDeviceStatus($id);
        if($device){
            return s("ok", [
                'status' => $device->status,
                'last_active' => $device->last_active
            ]);
        }else{
            return f(1);
        }
    }

    public static function getDeviceStatus(int $id)
    {
        $redis = Redis::connection('device');
        $device = $redis->get('device:' . $id);
        $device = json_decode($device);
        return $device;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getDevicesByLocationId(Request $request)
    {
        $this->validate($request, [
            'location_id' => 'required|integer'
        ]);
        $location_id = $request->input("location_id");
        $devices = (new Device())->where('location_id', '=', $location_id)->orderBy('floor')->get();
        return $devices;
    }

    private function sendMaintainNotification($deviceId, $desc, $locationDescription)
    {
        $task = new WechatOpenPlatNotifyTask(config("wechat.open_plat_maintain_template"),
            [
                'first' => [
                    'value' => '设备被报修：'.$deviceId,
                    'color' => '#FF0000',
                ],
                'keyword1' => [
                    'value' => '需要维护 '.$desc,
                    'color' => '#FF0000',
                ],
                'keyword2' => [
                    'value' => date("Y-m-d h:i:sa"),
                    'color' => '#000000',
                ],
                'remark' => [
                    'value' => $locationDescription,
                    'color' => '#000000',
                ]
            ]
        );
        Task::deliver($task);
    }

    public function reportDeviceProblem(Request $request)
    {
        $user = Auth::user();
        $this->validate($request,[
            'device_id' => 'required|integer'
        ]);
        $deviceId = $request->input("device_id");
        $device = Device::findOrFail($deviceId);
        $user->pull_down_times -= 5;
        $user->save();
        $desc = $request->input("desc");

        $report = new Report();
        $report->device_id = $deviceId;
        $report->desc = $desc;
        $report->save();
        $this->sendMaintainNotification($deviceId, $desc, $device->location_desc);
        return s("ok");
    }

}