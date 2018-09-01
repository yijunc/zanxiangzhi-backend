<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/23/18
 * Time: 11:28 PM
 */

namespace Junning\Sdk\usr;


class MQTTService
{

    protected $usrConnection;
    protected $isOn = false;
    protected $callbacks;

    public function __construct($options = null)
    {
        $this->callbacks = new CallbackPool();
        $this->usrConnection = new MQTTClient();
        $this->usrConnection->onConnect = function ($code, MQTTClient $client) {
            $this->isOn = true;
            $this->callbacks->callAll();
        };
        $this->usrConnection->onClose = function (MQTTClient $client) {
            $this->isOn = false;
            swoole_timer_after(config("usr.time_out_interval"), function(){
                $this->connect();
            });
        };
    }

    public function connect($callback = null)
    {
        if ($this->isOn) {
            if (is_callable($callback)) {
                call_user_func($callback);
            }
            return;
        }
        $this->usrConnection->connect(config('usr.usrAccount'), config('usr.usrPassword'));
        if(is_callable($callback)){
            $this->callbacks->push($callback);
        }
    }

    public function activate($device_tag, $activation_period)
    {
//        base_convert($activation_period, 10, 16);
        if($this->isOn){
            $this->usrConnection->publish('$USR/DevRx/' . $device_tag, $activation_period,
                function (MQTTClient $client) {

                });
        }else{
            $this->connect(function() use ($device_tag, $activation_period){
               $this->activate($device_tag, $activation_period);
            });
        }
    }


}