<?php

namespace Tests\WpMock\Mocks;

use WpMock\Mocks\OptionMock;

class OptionMockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return OptionMock
     */
    private function getSubject()
    {
        return $this->getMock(
            '\WpMock\Mocks\OptionMock',
            ['_'],
            [],
            '',
            false
        );
    }

    // Matches
    // -----------------------------------------------------------------------------------------------------------------

    public function testMatches()
    {
        // Prepare
        $s = new OptionMock('foo');
        $s->added();
        // Assert
        $this->assertTrue($s->matches('foo', OptionMock::MOD_ADDED));
        $this->assertFalse($s->matches('bar', OptionMock::MOD_ADDED));
        $this->assertFalse($s->matches('foo', OptionMock::MOD_UPDATED));
    }

    // Invoke and Verify
    // -----------------------------------------------------------------------------------------------------------------

    public function testVerifyWithoutModifier()
    {
        // Prepare
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException',
            'Option (foo) has no modifier set!'
        );
        $s = new OptionMock('foo');
        // Act
        $s->verify();
    }

    public function testVerifyWithoutInvoke()
    {
        // Prepare
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException',
            'Option (bar) was expected to be added but was not invoked.'
        );
        $s = new OptionMock('bar');
        $s->added();
        // Act
        $s->verify();
    }

    public function testVerifyInvokedWrongValue()
    {
        // Prepare
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException',
            'Option (baz) received wrong value (bar).'
        );
        $s = new OptionMock('baz');
        $s->added()->with('foo');
        // Act
        $s->invoke('bar');
        $s->verify();
    }

    public function testVerifyInvokedWrongAutoload()
    {
        // Prepare
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException',
            'Option (foo) received wrong autoload (no), expected yes.'
        );
        $s = new OptionMock('foo');
        $s->added()->isAutoload();
        // Act
        $s->invoke(null, false);
        $s->verify();
    }

    public function testVerifyOkAdded()
    {
        // Prepare
        $s = new OptionMock('foo');
        $s->added()->with('bar')->noAutoload();
        // Act
        $s->invoke('bar', false);
        $s->verify();
    }

    public function testVerifyOkUpdated()
    {
        // Prepare
        $s = new OptionMock('foo');
        $s->updated()->to('baz');
        // Act
        $s->invoke('baz');
        $s->verify();
    }

    // Setter exceptions
    // -----------------------------------------------------------------------------------------------------------------

    private function buildSetterExceptionTests($setter, $modifier, $allowedModifier)
    {
        // Prepare
        $currentModifier = is_null($modifier) ? 'nothing' : $modifier;
        $this->setExpectedException(
            'RuntimeException',
            "'{$setter}' usable only with modifier '{$allowedModifier}' not '{$currentModifier}'"
        );
        $subject = $this->getSubject();
        // Act
        if (!is_null($modifier)) {
            $subject->$modifier();
        }
        $subject->$setter('foo');
    }

    public function testExceptionWithoutModifierOnWith()
    {
        $this->buildSetterExceptionTests('with', null, 'added');
    }

    public function testExceptionWithoutModifierOnTo()
    {
        $this->buildSetterExceptionTests('to', null, 'updated');
    }

    public function testExceptionModifierUpdatedOnWith()
    {
        $this->buildSetterExceptionTests('with', 'updated', 'added');
    }

    public function testExceptionModifierRemovedOnWith()
    {
        $this->buildSetterExceptionTests('with', 'deleted', 'added');
    }

    public function testExceptionModifierAddedOnTo()
    {
        $this->buildSetterExceptionTests('to', 'added', 'updated');
    }

    public function testExceptionModifierRemovedOnTo()
    {
        $this->buildSetterExceptionTests('to', 'deleted', 'updated');
    }

    // Autoload exception
    // -----------------------------------------------------------------------------------------------------------------

    private function buildAutoloadExceptionTests($autoloader, $modifier, $allowedModifier)
    {
        // Prepare
        $currentModifier = is_null($modifier) ? 'nothing' : $modifier;
        $this->setExpectedException(
            'RuntimeException',
            "Autoload expectation usable only with modifier '{$allowedModifier}' not '{$currentModifier}'"
        );
        $subject = $this->getSubject();
        // Act
        if (!is_null($modifier)) {
            $subject->$modifier();
        }
        $subject->$autoloader();
    }

    public function testExceptionWithoutModifierOnIsAutoload()
    {
        $this->buildAutoloadExceptionTests('isAutoload', null, 'added');
    }

    public function testExceptionWithoutModifierOnNoAutoload()
    {
        $this->buildAutoloadExceptionTests('noAutoload', null, 'added');
    }

    public function testExceptionModifierUpdatedOnIsAutoload()
    {
        $this->buildAutoloadExceptionTests('isAutoload', 'updated', 'added');
    }

    public function testExceptionModifierUpdatedOnNoAutoload()
    {
        $this->buildAutoloadExceptionTests('noAutoload', 'updated', 'added');
    }

    public function testExceptionModifierRemovedOnIsAutoload()
    {
        $this->buildAutoloadExceptionTests('isAutoload', 'deleted', 'added');
    }

    public function testExceptionModifierRemovedOnNoAutoload()
    {
        $this->buildAutoloadExceptionTests('noAutoload', 'deleted', 'added');
    }
}
