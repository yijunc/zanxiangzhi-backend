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

    public function __construct($options = null)
    {
        $this->usrConnection = new MQTTClient();
        $this->usrConnection->onConnect = function (MQTTClient $client) {
            $this->isOn = true;
        };
        $this->usrConnection->onClose = function (MQTTClient $client) {
            $this->isOn = false;
        };
    }

    public function connect($callback = null)
    {
        echo "in __connect \n";
        if ($this->isOn) {
            if (is_callable($callback)) {
                call_user_func($callback);
            }
            return;
        }
        $this->usrConnection->connect(config('usr.usrAccount'), config('usr.usrPassword'));
    }

    public function activate($device_tag)
    {
        echo "in __activate \n";
        $this->usrConnection->publish('$USR/DevRx/' . $device_tag, config('usr.activationCode'),
            function (MQTTClient $client) {
                echo "adsfasdfafa\n";
        });

    }

    public function __call($name, $arguments)
    {
        echo "in __call \n";
        if ($this->isOn) {
            return call_user_func_array($name, $arguments);
        } else {
            $this->connect(function () use ($name, $arguments) {
                call_user_func_array($name, $arguments);
            });
            return true;
        }
    }

}