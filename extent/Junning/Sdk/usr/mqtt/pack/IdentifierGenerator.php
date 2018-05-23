<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 17:49
 */

namespace Junning\Sdk\usr\mqtt\pack;


class IdentifierGenerator
{
    protected static $size = 256*256;
    protected static $len = 2;
    protected static $now= 0;
    public static function get(){
        static::$now += 1;
        if(static::$now >= static::$size){
            static::$now = 1;
        }
        return Converter::editLength(
            Converter::decToBytes(static::$now),
            static::$len
        );
    }
}