<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 16:55
 */

namespace Junning\Sdk\usr\mqtt\unpack;

class FixUnpacker extends Unpacker
{
    public function __construct($data, $len = 1)
    {
        parent::__construct($data, $len);
    }

    protected function process()
    {
        $this->innerData = substr($this->data, 0, $this->lenSize);
        $this->leftData = substr($this->data, $this->lenSize);
    }

}