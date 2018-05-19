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
    }

}