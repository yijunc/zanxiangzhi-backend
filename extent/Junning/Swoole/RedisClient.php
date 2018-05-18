<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/15
 * Time: 1:29
 */

namespace Junning\Swoole;


class RedisClient
{
    protected $options;
    /**
     * @var \swoole_redis
     */
    protected $redis;
    protected $isOn = false;
    public function __construct($options=null)
    {
        $this->options = $options;
        $this->redis = new \swoole_redis($options);
        $this->redis->on("Close", [$this, 'onClose']);
    }

    public function connect($callback = null){
        if($this->isOn){
            if(is_callable($callback)){
                call_user_func($callback);
            }
            return;
        }
        $this->redis->connect($this->options['host'], $this->options['port'], function(\swoole_redis $client, $result) use ($callback){
            if($result === false){
                $this->isOn = false;
                swoole_timer_after($this->options['timeout']*1000, [$this, "connect"], $callback);
            }else{
                $this->isOn = true;
                if(is_callable($callback)){
                    call_user_func($callback);
                }
            }
        });
    }

    public function onClose(\swoole_redis $redis){
        $this->isOn = false;
    }

    public function __call($name, $arguments)
    {
        if($this->isOn){
            return call_user_func_array([$this->redis, $name], $arguments);
        }else{
            $this->connect(function() use ($name, $arguments){
                call_user_func_array([$this->redis, $name], $arguments);
            });
            return true;
        }
    }

}