<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/23/18
 * Time: 2:53 PM
 */

namespace App\Jobs\Tasks;


use Hhxsv5\LaravelS\Swoole\Task\Task;

class DeviceActivator extends Task
{
    protected $deviceTag;
    public function __construct($tag)
    {
        $this->deviceTag = $tag;
    }

    public function handle()
    {
        app('MQTTService')->activate($this->deviceTag);
    }
}