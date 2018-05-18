<?php

if(!function_exists("s")){
    /**
     * @param string $msg
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    function s($msg = "success", $data = null){
        return response()->json([
            'code' => 200,
            'msg' => $msg,
            'data' => $data
        ]);
    }
}

if(!function_exists("f")){
    /**
     * @param $code
     * @param string $msg
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    function f($code, $msg = "failed", $data = null){
        return response()->json([
            'code' => 400 + $code,
            'msg' => $msg,
            'data' => $data
        ]);
    }
}