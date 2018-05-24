<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/15
 * Time: 2:58
 */

namespace App\Providers;


use Hhxsv5\LaravelS\Swoole\WebsocketHandlerInterface;

class WebsocketHandler implements WebsocketHandlerInterface
{

    public function __construct()
    {
    }

    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        echo "[ info ] "."websocket client on: ".$request->fd;
    }

    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $ret = json_decode($frame->data);
        if(!empty($ret) && property_exists($ret, "controller") && property_exists($ret, "action")){
            try{
                $className = "App\\Http\\WebsocketControllers\\".$ret->controller."Controller";
                $controller = new $className($server, $frame, $ret);
                if(method_exists($controller, $ret->action)){
                    call_user_func_array([$controller,$ret->action], [$ret, $server, $frame]);
                }
            }catch (\Throwable $e){
                $this->printException($e);
            }
        }
    }

    public function onClose(\swoole_websocket_server $server, $fd, $reactorId)
    {
    }

    public function printException(\Throwable $e){
        echo "[ error ] ".$e->getMessage(). " (".$e->getFile().":".$e->getLine()."):"."\n";
        echo $e->getTraceAsString()."\n";
    }
}