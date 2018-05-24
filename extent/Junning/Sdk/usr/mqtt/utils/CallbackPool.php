<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 18:45
 */

namespace Junning\Sdk\usr\mqtt\utils;


class CallbackPool
{
    protected $pool = [];
    public function set($identifier, $callable){
        if(is_callable($callable)){
            $this->pool[$identifier] = $callable;
        }
    }

    public function get($identifier, $remove = true){
        if(isset($this->pool[$identifier])){
            $callable = clone $this->pool[$identifier];
            if($remove) unset($this->pool[$identifier]);
            return $callable;
        }
    }

    public function call($identifier, $thenRemove = true, ...$params){
        if(isset($this->pool[$identifier])){
            $callable = clone $this->pool[$identifier];
            if(is_callable($callable)){
                $callable(...$params);
            }
        }
        if($thenRemove) unset($this->pool[$identifier]);
    }
}