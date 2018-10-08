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

class WechatOpenPlatNotifyTask extends Task
{
    protected $templateId;
    protected $values;

    public function __construct($templateId, $values)
    {
        $this->templateId = $templateId;
        $this->values = $values;
    }

    public function handle()
    {
        $accessTokenMeta = (new Meta())->where('key','wechat_access_token')->findOrFail();
        $admins = Admin::all();
        foreach ($admins as $admin){
            AsyncCurl::postJSON('api.weixin.qq.com', 443, 'api.weixin.qq.com',
                '/cgi-bin/message/template/send?access_token='.$accessTokenMeta->value,
                    [
                            "touser" => $admin->openid,
                            "template_id" => $this->templateId,
                            "url" => "",
                            "topcolor" => "#FF0000",
                            "data" => $this->values,

                    ],
                function(){

                }
            );
        }

    }
}