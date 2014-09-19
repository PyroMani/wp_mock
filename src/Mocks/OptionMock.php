<?php

namespace WpMock\Mocks;

use WpMock\Exceptions\ExpectationFailedException;
use WpMock\Exceptions\OptionBuilderException;

class OptionMock
{
    private $key              = null;

    // Expectations
    private $expectedModifier = self::MOD_NOTHING;
    private $expectedValue    = null;
    private $expectedAutoload = null;

    // Invoked
    private $invoked          = false;
    private $invokedValue     = null;
    private $invokedAutoload  = null;

    // Modifiers
    const MOD_NOTHING         = 'nothing';
    const MOD_ADDED           = 'added';
    const MOD_UPDATED         = 'updated';
    const MOD_DELETED         = 'deleted';

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Check if this OptionMock matches
     * @param string $key Invoked key
     * @param string $modifier Invoked modifier
     * @return bool true if $key and $modifier match
     */
    public function matches($key, $modifier)
    {
        return $this->key === $key
            && $this->expectedModifier == $modifier;
    }

    public function invoke($value = null, $autoload = null)
    {
        $this->invoked = true;
        $this->invokedValue = $value;
        $this->invokedAutoload = $autoload;
    }

    public function verify()
    {
        // Has modifier
        if ($this->expectedModifier === self::MOD_NOTHING) {
            throw new ExpectationFailedException(
                "Option ({$this->key}) has no modifier set!"
            );
        }
        // Invoked
        if (!$this->invoked) {
            throw new ExpectationFailedException(
                "Option ({$this->key}) was expected to be {$this->expectedModifier} but was not invoked."
            );
        }
        // Expected value
        if (!is_null($this->expectedValue)
            && $this->expectedValue !== $this->invokedValue
        ) {
            throw new ExpectationFailedException(
                "Option ({$this->key}) received wrong value ({$this->invokedValue})."
            );
        }
        // Expected autoload
        if (!is_null($this->expectedAutoload)
            && $this->expectedAutoload !== $this->invokedAutoload
        ) {
            $expectedAutoload = $this->expectedAutoload ? 'yes' : 'no';
            $invokedAutoload  = $this->invokedAutoload ? 'yes' : 'no';
            throw new ExpectationFailedException(
                "Option ({$this->key}) received wrong autoload ({$invokedAutoload}), expected {$expectedAutoload}."
            );
        }
    }

    // C_UD expectations
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Expect the option to be set
     * @return OptionMock
     */
    public function added()
    {
        $this->expectedModifier = self::MOD_ADDED;
        return $this;
    }

    /**
     * Expect the option to be updated
     * @return OptionMock
     */
    public function updated()
    {
        $this->expectedModifier = self::MOD_UPDATED;
        return $this;
    }

    /**
     * Expect the option to be removed
     * @return OptionMock
     */
    public function deleted()
    {
        $this->expectedModifier = self::MOD_DELETED;
        return $this;
    }

    // Value expectations
    // -----------------------------------------------------------------------------------------------------------------

    public function with($value)
    {
        if ($this->expectedModifier !== self::MOD_ADDED) {
            throw OptionBuilderException::valueExpectationException('with', self::MOD_ADDED, $this->expectedModifier);
        }
        $this->expectedValue = $value;
        return $this;
    }

    public function to($value)
    {
        if ($this->expectedModifier !== self::MOD_UPDATED) {
            throw OptionBuilderException::valueExpectationException('to', self::MOD_UPDATED, $this->expectedModifier);
        }
        $this->expectedValue = $value;
        return $this;
    }

    // Autoload expectations
    // -----------------------------------------------------------------------------------------------------------------

    public function isAutoload()
    {
        if ($this->expectedModifier !== self::MOD_ADDED) {
            throw OptionBuilderException::autoloadExpectationException(self::MOD_ADDED, $this->expectedModifier);
        }
        $this->expectedAutoload = true;
        return $this;
    }

    public function noAutoload()
    {
        if ($this->expectedModifier !== self::MOD_ADDED) {
            throw OptionBuilderException::autoloadExpectationException(self::MOD_ADDED, $this->expectedModifier);
        }
        $this->expectedAutoload = false;
        return $this;
    }
}
