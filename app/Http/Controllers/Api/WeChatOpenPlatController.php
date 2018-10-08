<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 10/8/18
 * Time: 7:29 PM
 */

namespace App\Http\Controllers\Api;


use App\Models\Meta;
use Illuminate\Http\Request;

class WeChatOpenPlatController
{
    public function process(Request $request){
        $msg = $request->getContent();

        //var_dump($msg);

        $meta = new Meta();
        $meta->key = 'latest_msg';
        $meta->value = $msg;

        $meta->save();

        return 'success';
     }

     public function firstTimeTokenReply(Request $request){

        return $request->get("echostr");

     }
}