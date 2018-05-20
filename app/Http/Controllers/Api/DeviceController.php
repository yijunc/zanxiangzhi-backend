<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/17
 * Time: 22:23
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getStatus(Request $request){
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = $request->input("id");
        $device = Device::fetchOrFailed($id);
        return s("ok", [
           'status' => $device->status,
           'last_active' => $device->updated_at
        ]);
    }

}