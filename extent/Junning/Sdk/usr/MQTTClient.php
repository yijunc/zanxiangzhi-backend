<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/3
 * Time: 23:47
 */

namespace Junning\Sdk\usr;

use Junning\Sdk\usr\mqtt\pack\Converter;
use Junning\Sdk\usr\mqtt\pack\FixedHeader;
use Junning\Sdk\usr\mqtt\pack\IdentifierGenerator;
use Junning\Sdk\usr\mqtt\pack\Packer;
use Junning\Sdk\usr\mqtt\unpack\FixUnpacker;
use Junning\Sdk\usr\mqtt\unpack\IdentifierUnpacker;
use Junning\Sdk\usr\mqtt\unpack\Unpacker;
use Junning\Sdk\usr\mqtt\utils\CallbackPool;
use Swoole\Client;

class MQTTClient
{
    protected $client;
    protected $host = "clouddata.usr.cn";
   // protected $host = "127.0.0.1";
    protected $port = 1883;
    protected $heatbeatInterval = 30;
    protected $username;
    protected $password;
    protected $callbacks;
    public $onConnect;
    public $onError;
    public $onClose;
    public $onOther;
    public function __construct()
    {
        $this->client = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $this->client->set([
            'open_length_check' => true,
            'package_length_type' => 'C',
            'package_length_offset' => 1,
            'package_body_offset' => 2,
            'package_max_length'=> 800000
        ]);
        $this->client->on("Connect", [$this, '__onInnerConnect']);
        $this->client->on("Receive", [$this, '__onInnerReceive']);
        $this->client->on("Error", [$this, "__onInnerError"]);
        $this->client->on("Close", [$this, "__onInnerClose"]);
        $this->callbacks = new CallbackPool();
        swoole_timer_tick($this->heatbeatInterval*1000, function(){
            $this->ping();
        });
    }

    public function connect($username, $password){
        $this->username = $username;
        $this->password = $password;
        $this->client->connect($this->host, $this->port);
    }

    public function disconnect(){
        $this->send(null, 0xE0);
        $this->client->close();
    }

    public function subscribe($topicFilter, $QoS = 0x00, $callback = null){
        $identifier = IdentifierGenerator::get();
        $serPacker = new Packer($topicFilter, 2);
        $ser2 = Converter::a2d([$QoS]);
        $this->callbacks->set($identifier, $callback);
        $this->send($identifier.$serPacker->pack().$ser2, 0x82);
    }

    public function unsubscribe($topicFilter, $callback = null){
        $identifier = IdentifierGenerator::get();
        $serPacker = new Packer($topicFilter, 2);
        $this->callbacks->set($identifier, $callback);
        $this->send($identifier.$serPacker->pack(), 0xA2);
    }

    public function publish($topic, $data, $callback = null){
        $ser1Packer = new Packer($topic, 2);
        $identifier = IdentifierGenerator::get();
        $this->callbacks->set($identifier, $callback);
        $this->send($ser1Packer->pack().$identifier.$data, 0x32);
    }

    protected function send($data, $header = 0x10){
        $fixed = new FixedHeader($data, $header);
        echo "I SENT: ";
        Converter::show($fixed->pack());
        return $this->client->send($fixed->pack());
    }

    protected function ping(){
        $this->send(null, 0xC0);
    }

    protected function connectPack(){
        $ser1 = [0x00, 0x04, 0x4D, 0x51, 0x54, 0x54, 0x04, 0xC2, 0x02, 0x58];
        $ser1 = Converter::a2d($ser1);
        $ser2Packer = new Packer("APP:".$this->username, 2);
        $ser3Packer = new Packer($this->username, 2);
        $ser4Packer = new Packer(md5($this->password), 2);
        return $ser1.$ser2Packer->pack().$ser3Packer->pack().$ser4Packer->pack();
    }

    public function __onInnerConnect(Client $client){
         $this->send($this->connectPack());
    }

    public function __onInnerReceive(Client $client, $data){
        Converter::show($data);
        $codeUpk = new FixUnpacker($data, 1);
        $code = $codeUpk->unpack();
        $bodyUpk = new Unpacker($codeUpk->left(), 1);
        $body = $bodyUpk->unpack();
        switch (ord($code)) {
            case 0xD0:
                break;
            case 0x20:
                $this->__onInnerMQTTConnectAck($body);
                break;
            case 0x92:
                $this->__onInnerMQTTSubscribeAck($body);
                break;
            case 0xB0:
                $this->__onInnerMQTTUnsubscribeAck($body);
                break;
            case 0x40:
                $this->__onInnerMQTTPublishAck($body);
                break;
            default:
                echo "unknown code: ";
                Converter::show($code);
                break;
        }
    }

    public function __onInnerMQTTConnectAck($body){
        $fix1 = new FixUnpacker($body,1);
        $fix2 = new FixUnpacker($fix1->left(), 1);
        if(is_callable($this->onConnect)){
            ($this->onConnect)(ord($fix2->unpack()), $this);
        }
    }

    public function __onInnerMQTTSubscribeAck($body){
        $identifierUpk = new IdentifierUnpacker($body);
        $identifier = $identifierUpk->unpack();
        $code = $identifierUpk->left();
        $this->callbacks->call($identifier, true, ord($code), $this);
    }

    public function __onInnerMQTTUnsubscribeAck($body){
        $identifierUpk = new IdentifierUnpacker($body);
        $identifier = $identifierUpk->unpack();
        $this->callbacks->call($identifier, true, $this);
    }

    public function __onInnerMQTTPublishAck($body){
        $identifierUpk = new IdentifierUnpacker($body);
        $identifier = $identifierUpk->unpack();
        $this->callbacks->call($identifier, true, $this);
    }

    public function __onInnerError(Client $client){
        echo "error!"."\n";
        if(is_callable($this->onError)) {
            ($this->onError)(0, "unknown", $this);
        }
    }

    public function __onInnerClose(Client $client){
        echo "onClose!\n";
        if(is_callable($this->onClose)){
            ($this->onClose)($this);
        }
    }
}