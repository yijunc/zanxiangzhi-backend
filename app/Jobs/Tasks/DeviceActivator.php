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
    protected $deviceId;
    public function __construct($id)
    {
        $this->deviceId = $id;
    }

    public function handle()
    {
        // TODO: Implement handle() method.
    }
}