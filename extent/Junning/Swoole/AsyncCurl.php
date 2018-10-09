<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/16
 * Time: 0:08
 */

namespace Junning\Swoole;


class AsyncCurl
{

    public static function run($host, $port, $domain, $path, $callback, $post = false, $data = [], $ssl = true, $timeout = 1){
        if(!Curl::isIp($host)){
            swoole_async_dns_lookup($host, function($origin, $ip) use ($port, $domain, $path, $callback, $post, $data, $ssl, $timeout){
                self::innerRun($ip, $port, $domain, $path, $callback, $post, $data, $ssl, $timeout);
            });
        }else{
            self::innerRun($host, $port, $domain, $path, $callback, $post = false, $data = [], $ssl = true, $timeout = 1);
        }
    }

    private static function innerRun($host, $port, $domain, $path, $callback, $post = false, $data = [], $ssl = true, $timeout = 1){
        $cli = new \swoole_http_client($host, $port, $ssl);
        $cli->setHeaders([
            'Host' => $domain,
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json,text/json',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => $timeout]);
        if ($post) {
            $cli->post($path, $data, $callback);
        } else {
            $params = '';
            foreach ($data as $key => $value) {
                $params .= $key . "=" . urlencode($value) . "&";
            }
            rtrim($params, "&");
            $cli->get($path . (empty($params) ? "" : "?" . $params), $callback);
        }
    }

    public static function get($host, $port, $domain, $path, $data = [], $callback, $ssl = true, $timeout = 1){
        self::run($host, $port, $domain, $path, $callback,false, $data, $ssl, $timeout);
    }


    public static function post($host, $port, $domain, $path, $data = [], $callback, $ssl = true, $timeout = 1){
        self::run($host, $port, $domain, $path, $callback,true, $data, $ssl, $timeout);
    }

}