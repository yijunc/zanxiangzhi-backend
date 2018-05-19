<?php
/**
 * Created by PhpStorm.
 * User: zhaoning
 * Date: 2018/5/19
 * Time: 下午3:17
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getLeftTimes(){
        $user = Auth::user();
        $last_pull_down = date('Y-m-d',$user->last_pull_down);
        $pull_down_times = $user->pull_down_times;
        $times = config('app.pull_down_times_per_day');
        $today = date('Y-m-d');
        if($last_pull_down!=$today) {
            $pull_down_times = $times;
        }
        return s("ok",[
            "pull_down_times" => $pull_down_times
        ]);
    }

}