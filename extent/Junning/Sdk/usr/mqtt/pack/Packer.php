<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 0:19
 */

namespace Junning\Sdk\usr\mqtt\pack;


class Packer
{
    protected $lenSize;
    protected $dataSize = 0;
    protected $data;
    protected $head;
    public function __construct($data, $lenSize = 1)
    {
        $this->lenSize = $lenSize;
        $this->dataSize = strlen($data);
        $this->data = $data;
        $this->process();
    }

    protected function process(){
        $this->head = Converter::editLength (
            Converter::decToBytes($this->dataSize),
            $this->lenSize
        );
    }

    public function pack(){
        return $this->head.$this->data;
    }
}