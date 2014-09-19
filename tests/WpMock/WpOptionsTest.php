<?php

namespace Tests;

use WpMock\WpOptions;

class WpOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        WpOptions::reset();
    }

    public function testVerify()
    {
        // Prepare
        WpOptions::expects('foo')->added();
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException'
        );
        WpOptions::expects('bar')->updated();
        // Assert
        WpOptions::verify();
    }

    public function testAddOption()
    {
        // Prepare
        WpOptions::expects('foo')->added()->with('bar')->noAutoload();
        // Act
        \add_option('foo', 'bar', null, 'no');
        // Assert
        WpOptions::verify();
    }

    public function testUpdateOption()
    {
        // Prepare
        WpOptions::expects('foo')->updated();
        // Act
        \update_option('foo', null);
        // Assert
        WpOptions::verify();
    }

    public function testDeleteOption()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'bar');
        WpOptions::expects('foo')->deleted();
        // Act
        $this->assertEquals('bar', \get_option('foo'));
        \delete_option('foo');
        // Assert
        WpOptions::verify();
    }

    public function testPrepareOption()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'bar');
        // Assert
        $this->assertEquals('bar', \get_option('foo'));
    }

    public function testPrepareOptions()
    {
        // Prepare
        WpOptions::prepareOptions(
            [
                'foo' => [ 'value' => 'bar', 'autoload' => 'no' ],
                'bar' => 'baz',
            ]
        );
        // Assert
        $this->assertEquals('bar', \get_option('foo'));
        $this->assertEquals('baz', \get_option('bar'));
    }

    public function testGetOption()
    {
        $this->assertFalse(\get_option('foo'));
    }

    public function testModifiersWrongKey()
    {
        $this->assertFalse(\get_option(null));
        $this->assertFalse(\update_option(null, null));
        $this->assertFalse(\add_option(null, null));
        $this->assertFalse(\delete_option(null));
    }

    public function testUpdateOptionKeyAlreadySet()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'baz');
        // Assert
        $this->assertEquals('baz', \get_option('foo'));
        $this->assertTrue(\update_option('foo', 'bar'));
        $this->assertEquals('bar', \get_option('foo'));
    }

    public function testUpdateOptionValueAlreadySet()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'bar');
        // Assert
        $this->assertEquals('bar', \get_option('foo'));
        $this->assertFalse(\update_option('foo', 'bar'));
        $this->assertEquals('bar', \get_option('foo'));
    }

    public function testAddOptionKeyAlreadySet()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'bar');
        // Assert
        $this->assertFalse(\add_option('foo', 'baz'));
        $this->assertEquals('bar', \get_option('foo'));
    }

    public function testDeleteOptionWhenKeyNotSet()
    {
        $this->assertFalse(\delete_option('foo'));
    }

    public function testSerialize()
    {
        // Prepare
        $obj = new \stdClass();
        $obj->foo = 'bar';
        // Act
        \add_option('foo', $obj);
        // Assert
        $this->assertEquals($obj, \get_option('foo'));
    }
}
