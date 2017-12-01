<?php

namespace Broco\Campaigner\Soap\Model;

/**
 * Class CustomAttribute
 *
 * @package Broco\Campaigner\Soap\Model
 */
class CustomAttribute
{
    /**
     * @var int
     */
    public $Id;

    /**
     * @var bool
     */
    public $IsNull;

    /**
     * @var string
     */
    public $_;

    /**
     * CustomAttribute constructor.
     *
     * @param int    $Id
     * @param bool   $IsNull
     * @param string $value
     */
    public function __construct($Id, $IsNull, $value)
    {
        $this->Id = $Id;
        $this->IsNull = $IsNull;
        $this->_ = $value;
    }
}
