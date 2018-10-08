<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/23/18
 * Time: 3:00 PM
 */

namespace App\Jobs\Timers;


use App\Models\Device;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

class DeviceCheckActiveJob extends CronJob
{
    protected $interval = 60 * 60 * 1000;

    public function __construct()
    {
        echo "[ info ] " . 'device-checking workers at an interval of ' . $this->interval . "ms\n";
    }

    /**
     * @return int $interval ms
     */
    public function interval()
    {
        return $this->interval;
    }

    public function run()
    {
        //zn shang
    }



}