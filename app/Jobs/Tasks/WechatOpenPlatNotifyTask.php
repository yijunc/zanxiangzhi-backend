<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 10/8/18
 * Time: 5:02 PM
 */

namespace App\Jobs\Tasks;


use App\Models\Admin;
use App\Models\Meta;
use function foo\func;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Junning\Swoole\AsyncCurl;
use Junning\Swoole\Curl;

class WechatOpenPlatNotifyTask extends Task
{
    protected $templateId;
    protected $values;

    public function __construct($templateId, $values)
    {
        $this->templateId = $templateId;
        $this->values = $values;
    }

//    //参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
//    private function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
//    {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
//        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
//        if ($post) {
//            curl_setopt($curl, CURLOPT_POST, 1);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(json_encode($post)));
//        }
//        if ($cookie) {
//            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
//        }
//        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
//        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $data = curl_exec($curl);
//        if (curl_errno($curl)) {
//            return curl_error($curl);
//        }
//        curl_close($curl);
//        if ($returnCookie) {
//            list($header, $body) = explode("\r\n\r\n", $data, 2);
//            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
//            $info['cookie'] = substr($matches[1][0], 1);
//            $info['content'] = $body;
//            return $info;
//        } else {
//            return $data;
//        }
//    }

    private function postJSON($url, $data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
        ));

        $result = curl_exec($ch);
        return $result;
    }

    public function handle()
    {
        $accessTokenMeta = (new Meta())->where('key', 'wechat_access_token')->firstOrFail();
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $retData = $this->postJSON('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessTokenMeta->value,
                [
                    "touser" => $admin->openid,
                    "template_id" => $this->templateId,
                    "url" => '',
                    "topcolor" => "#FF0000",
                    "data" => $this->values,
                ]);
            var_dump($retData);
        }

    }
}