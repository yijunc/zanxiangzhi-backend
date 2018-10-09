<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/13
 * Time: 19:42
 */

namespace Junning\Swoole;

use Swoole\Coroutine;

class Curl
{
    public static function run($host, $port, $domain, $path, $post = false, $data = [], $ssl = true, $timeout = 1)
    {
        if(!self::isIp($host)){
            $host = Coroutine::getHostByName($host);
        }
        $cli = new \Swoole\Coroutine\Http\Client($host, $port, $ssl);
        $cli->setHeaders([
            'Host' => $domain,
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json,text/json',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => $timeout]);
        if ($post) {
            $cli->post($path, $data);
        } else {
            $params = '';
            foreach ($data as $key => $value) {
                $params .= $key . "=" . urlencode($value) . "&";
            }
            rtrim($params, "&");
            $cli->get($path . (empty($params) ? "" : "?" . $params));
        }
        $cli->close();
        return $cli->body;
    }


    public static function get($host, $port, $domain, $path, $data = [], $ssl = true, $timeout = 1){
        return self::run($host, $port, $domain, $path, false, $data, $ssl, $timeout);
    }

    public static function post($host, $port, $domain, $path, $data = [], $ssl = true, $timeout = 1){
        return self::run($host, $port, $domain, $path, true, $data, $ssl, $timeout);
    }

    public static function isIp($ip){
        if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))
        {
            return true;
        }else{
            return false;
        }
    }
}