<?php

namespace Broco\Campaigner\Soap\Model;

/**
 * Class NullableString
 *
 * @package Broco\Campaigner\Soap\Model
 */
class NullableString
{
    /**
     * @var bool
     */
    public $IsNull;

    /**
     * @var string
     */
    public $_;

    /**
     * NullableString constructor.
     *
     * @param bool   $IsNull
     * @param string $value
     */
    public function __construct($IsNull, $value)
    {
        $this->IsNull = $IsNull;
        $this->_ = $value;
    }
}
