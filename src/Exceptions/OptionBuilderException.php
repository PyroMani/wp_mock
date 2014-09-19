<?php

namespace WpMock\Exceptions;

use RuntimeException;

class OptionBuilderException extends RuntimeException
{
    public static function valueExpectationException($valueExpectation, $allowedModifier, $currentModifier)
    {
        return new RuntimeException(
            "'{$valueExpectation}' usable only with modifier '{$allowedModifier}' not '{$currentModifier}'"
        );
    }

    public static function autoloadExpectationException($allowedModifier, $currentModifier)
    {
        return new RuntimeException(
            "Autoload expectation usable only with modifier '{$allowedModifier}' not '{$currentModifier}'"
        );
    }
}
