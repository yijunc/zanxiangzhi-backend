<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/23/18
 * Time: 11:04 PM
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Junning\Sdk\usr\MQTTService;

class MQTTServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register("MQTTService", function () {
            return new MQTTService();
        });
    }
}