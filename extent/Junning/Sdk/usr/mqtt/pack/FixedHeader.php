<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 1:02
 */

namespace Junning\Sdk\usr\mqtt\pack;


class FixedHeader extends Packer
{
    protected $lenSize = 1;
    protected $header;
    public function __construct($data, $header = 0x10)
    {
        $this->header = $header;
        parent::__construct($data, 1);
    }

    public function pack()
    {
        $head = Converter::a2d([$this->header]);
        return $head.parent::pack();
    }
}