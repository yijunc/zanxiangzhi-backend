<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 10/8/18
 * Time: 3:56 PM
 */

namespace App\Jobs\Timers;


use App\Models\Meta;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Junning\Swoole\AsyncCurl;

class WeAccessTokenRefresh extends CronJob
{
    protected $interval = 7200000;
    public function __construct()
    {
        $this->run();
        echo "[ info ] ".'access token refreshing workers at an interval of '.$this->interval."ms\n";
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
        AsyncCurl::get('api.weixin.qq.com', 443, 'api.weixin.qq.com', '/cgi-bin/token', [
            'grant_type' => 'client_credential',
            'appid' => config("wechat.open_plat_id"),
            'secret' => config("wechat.open_plat_secret")
        ], function($data){
           $data = json_decode($data->body)->access_token;
           // var_dump($data);
           $meta = Meta::where('key', 'wechat_access_token')->firstOrFail();
           $meta->value = $data;
           $meta->save();
        });
    }
}