<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2018/5/4
 * Time: 19:08
 */

namespace Junning\Sdk\usr\mqtt\unpack;


class IdentifierUnpacker extends FixUnpacker
{
    public function __construct($data)
    {
        parent::__construct($data, 2);
    }
}
