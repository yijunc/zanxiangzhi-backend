<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/24/18
 * Time: 3:37 PM
 */

namespace Junning\Sdk\usr;


class CallbackPool
{
    protected $pool;

    public function __construct()
    {
        $this->pool = [];
    }

    function push($callback){
        if(is_callable($callback)){
            array_push($this->pool, $callback);
        }
    }

    function callAll(){
        while($callback = array_pop($this->pool)){
            call_user_func($callback);
        }
    }
}