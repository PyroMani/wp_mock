<?php

namespace WpMock\Exceptions;

class ExpectationFailedException extends \PHPUnit_Framework_ExpectationFailedException
{
    public function __construct($msg)
    {
        parent::__construct($msg, null, null);
    }
}
