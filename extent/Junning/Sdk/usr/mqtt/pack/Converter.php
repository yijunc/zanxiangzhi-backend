<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 1:06
 */

namespace Junning\Sdk\usr\mqtt\pack;


class Converter
{
    public static function a2d(array $intArray){
        $res = '';
        foreach ($intArray as $int){
            $res .= chr($int);
        }
        return $res;
    }

    public static function show($data){
        echo "{";
        for ($i = 0; $i<strlen($data); $i++){
            echo ord($data[$i]).", ";
        }
        echo "end}"."\n";
    }

    public static function editLength($str, $length){
        $nowLen = strlen($str);
        if($nowLen > $length){
            return substr($str, $nowLen-$length);
        }else{
            for($i = 0; $i<($length - $nowLen); $i++){
                $str = chr(0).$str;
            }
            return $str;
        }
    }

    public static function bytesToDec($lenPart){
        $slen = strlen($lenPart);
        $result = 0;
        for ($i = 0; $i<$slen; $i++){
            $num = $lenPart[$slen - $i - 1];
            $num = ord($num);
            $result += $num*(intval(pow(256, $i)+0.5));
        }
        return $result;
    }

    public static function decToBytes($number){
        $head = '';
        $num = $number;
        while($num>0){
            $result = self::divide($num, 256);
            $num = $result[0]; $left = $result[1];
            $head = chr($left).$head;
        }
        return $head;
    }

    private static function divide($num1, $num2){
        return [intval(floor($num1/$num2)), $num1%$num2];
    }

}