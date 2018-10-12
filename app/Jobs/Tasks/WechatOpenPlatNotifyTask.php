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