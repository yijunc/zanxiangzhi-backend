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
use App\Models\Device;
use App\Models\Meta;
use App\Models\User;
use App\Models\UserRecord;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getLeftTimes()
    {
        $user = Auth::user();
        $lastPullDown = date('Y-m-d', $user->last_pull_down);
        $pullDownTimes = $user->pull_down_times;
        $times = config('app.pull_down_times_per_day');
        $today = date('Y-m-d');
        $leftTimes = $times - $pullDownTimes;
        if ($lastPullDown != $today) {
            $leftTimes = $times;
        }
        return s("ok", [
            "left_times" => $leftTimes
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function useToiletPaper(Request $request)
    {
        /**
         * @var User $user
         */
        //判断机器可用
        $this->validate($request, [
            'device_id' => 'required|integer'
        ]);
        $device_id = $request->input("device_id");
        if (DeviceController::getDeviceStatus($device_id)->status == 0) {
            return f(1, "device unavailable");
        }
        //判断用户剩余抽纸次数并验证
        $user = Auth::user();
        $ret = $this->getLeftTimes();
        $ret = $ret->content();
        $retLeftTimes = json_decode($ret);
        $leftTimes = $retLeftTimes->data->left_times;
        $times = config('app.pull_down_times_per_day');
        if ($leftTimes == 0) {
            return f(2);
        }
        $user->last_pull_down = time();
        $user->pull_down_times = $times - $leftTimes + 1;
        $user->save();

        // Push activation request into task queue.
        $task = new DeviceActivator($request->input("id"));
        Task::deliver($task);

        //增加用户记录
        $userRecord = new UserRecord();
        $userRecord->create([
            "user_id" => $user->id,
            "device_id" => $device_id,
            "type" => 0,
            "status" => 0
        ]);

        //点赞元数据增加
        $meta = (new Meta())->findOrFail($device_id);
        $meta->value += 1;
        $meta->saveOrFail();
        //机器使用次数更新
        $device = (new Device())->findOrFail($device_id);
        $device->update(["used_count" => $device->used_count + 1]);
        //返回剩余可用次数
        return s("ok", [
            "left_times" => $leftTimes - 1
        ]);
    }

}