<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 15:50
 */

namespace Junning\Sdk\usr\mqtt\unpack;


use Junning\Sdk\usr\mqtt\pack\Converter;

class Unpacker
{
    protected $lenSize;
    protected $data;
    protected $innerData;
    protected $leftData;
    public function __construct($data, $lenSize = 1)
    {
        $this->data = $data;
        $this->lenSize = $lenSize;
        $this->process();
    }

    public function unpack(){
        return $this->innerData;
    }

    public function left(){
        return $this->leftData;
    }

    protected function process(){
        $lenPart = substr($this->data, 0, $this->lenSize);
        $len = Converter::bytesToDec($lenPart);
        $this->innerData = substr($this->data, $this->lenSize, $len);
        $this->leftData = substr($this->data, $this->lenSize+$len);
    }

}