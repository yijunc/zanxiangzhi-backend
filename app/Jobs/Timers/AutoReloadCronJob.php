<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/10
 * Time: 13:52
 */

namespace App\Jobs\Timers;


use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

class AutoReloadCronJob extends CronJob
{

    protected $interval = 5000;
    public function __construct()
    {
        echo "[ info ] ".'auto-reloading workers at an interval of '.$this->interval."ms\n";
    }

    /**
     * @return int $interval ms
     */
    public function interval()
    {
        return $this->interval;
    }

    /**
     * @return bool $isImmediate
     */
    public function isImmediate()
    {
        return false;
    }

    public function run()
    {
        app("swoole")->reload();
    }


}