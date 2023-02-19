<?php

namespace Equip;

use Exception;

class ResponseErrorException extends Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}