<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/15
 * Time: 3:33
 */

namespace App\Http\WebsocketControllers;


class WsController
{
    protected $server;
    protected $frame;
    protected $data;
    public function __construct(\swoole_websocket_server $server, \swoole_websocket_frame $frame, $data)
    {
        $this->server = $server;
        $this->frame = $frame;
        $this->data = $data;
    }

    protected function send($data, $msg = null, $code = 200){
        $data = [
            'controller' => $this->data->controller,
            'action' => $this->data->action,
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        if($this->server->exist($this->frame->fd)){
            $this->server->push($this->frame->fd, json_encode($data));
        }
    }

    protected function success($data=null, $msg = "success"){
        $this->send($data, $msg);
    }

    protected function fail($msg, $code = 0, $data = null){
        $this->send($data, $msg, 400+$code);
    }

    protected function close(){
        $this->server->close($this->frame->fd);
    }
}