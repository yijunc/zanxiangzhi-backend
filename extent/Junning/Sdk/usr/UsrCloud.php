<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 20:09
 */

namespace Junning\Sdk\UsrCloud;


class UsrCloud
{
    /**
     * @var MQTTClient
     */
    protected $client;

    public function __construct($username, $password)
    {
        $this->client = new MQTTClient();
    }

}