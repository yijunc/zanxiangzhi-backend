<?php
/**
 * Created by PhpStorm.
 * User: zhaoning
 * Date: 2018/5/19
 * Time: 下午3:17
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Jobs\Tasks\DeviceActivator;
use App\Jobs\Tasks\WechatOpenPlatNotifyTask;
use App\Models\Device;
use App\Models\Meta;
use App\Models\User;
use App\Models\UserRecord;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Location;

class UserController extends Controller
{
    public function verifyToken()
    {
        return s();
    }

    public function getLeftTimes()
    {
        $user = Auth::user();
        $lastPullDown = date('Y-m-d', $user->last_pull_down);
        $pullDownTimes = is_null($user->pull_down_times) ? 0 : $user->pull_down_times;
        $times = config('app.pull_down_times_per_day');
        $today = date('Y-m-d');
        $leftTimes = $times - $pullDownTimes;
        if($leftTimes <= 0){
            $leftTimes = 0;
        }
        if ($lastPullDown != $today) {
            $leftTimes = $times;
        }
        return s("ok", [
            "left_times" => $leftTimes
        ]);
    }

    private function sendMaintainNotification($deviceId, $locationDescription)
    {
        $task = new WechatOpenPlatNotifyTask(config("wechat.open_plat_maintain_template"),
            [
                'first' => [
                    'value' => 'Device Low Paper. Id:'.$deviceId,
                    'color' => '#FF0000',
                ],
                'keyword1' => [
                    'value' => 'Need Maintenance',
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function useToiletPaper(Request $request)
    {
        /**
         * @var User $user
         */
        //判断机器可用
        $this->validate($request, [
            'device_id' => 'required|integer',
            'period' => 'required|integer|min:1|max:4'
        ]);
        $device_id = $request->input("device_id");
        $activation_period = $request->input("period");
        $deviceStatus = DeviceController::getDeviceStatus($device_id);
        if (is_null($deviceStatus) || $deviceStatus->status == 0) {
            return f(1, "device unavailable");
        }

        $device = (new Device())->findOrFail($device_id);
        if (is_null($device->left_segment_count) || $device->left_segment_count - $activation_period < 0) {
            $this->sendMaintainNotification($device->id, $device->location_desc);
            return f(1, "device unavailable");
        }

        $activation_period_code = '1' . str_repeat('0', $activation_period + 1);

        //判断用户剩余抽纸次数并验证
        $user = Auth::user();
        $ret = $this->getLeftTimes();
        $ret = $ret->content();
        $retLeftTimes = json_decode($ret);
        $leftTimes = $retLeftTimes->data->left_times;
        $times = config('app.pull_down_times_per_day');
        if ($leftTimes <= 0) {
            return f(2);
        }
        $user->last_pull_down = time();
        $user->pull_down_times = $times - $leftTimes + $activation_period;
        $user->save();

        // Push activation request into task queue.
        $target_device = Device::find($request->input('device_id'));
        app('MQTTService')->activate($target_device->tag, $activation_period_code);

        //增加用户记录
        $userRecord = new UserRecord();
        $userRecord->create([
            "user_id" => $user->id,
            "device_id" => $device_id,
            "type" => $activation_period,
            "status" => 0
        ]);

        //点赞元数据增加
        $meta = Meta::where('key', 'device_used_count')->firstOrFail();
        $meta->value += 1;
        $meta->saveOrFail();

        //机器使用次数更新
        $device = (new Device())->findOrFail($device_id);
        $device->update(["used_count" => $device->used_count + 1]);

        if(is_null($device->left_segment_count) || $device->left_segment_count - $activation_period <= 0) {
            $device->update(["left_segment_count" => 0]);
            $this->sendMaintainNotification($device->id, $device->location_desc);
        } else {
            $device->update(["left_segment_count" => $device->left_segment_count - $activation_period]);
        }

        //返回剩余可用次数和点赞总数
        return s("ok", [
            "left_times" => $leftTimes - $activation_period,
            "thumbs_up_count" => $meta->value
        ]);
    }

}