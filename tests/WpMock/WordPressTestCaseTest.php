<?php

namespace Tests;

use WpMock\WordPressTestCase;
use WpMock\WpOptions;

class WordPressTestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testSetUpClearsWpOptions()
    {
        // Prepare
        WpOptions::prepareOption('foo', 'bar');
        $this->assertEquals('bar', \get_option('foo'));
        // Act
        $s = new WordPressTestCase();
        $s->setUp();
        // Assert
        $this->assertFalse(\get_option('foo'));
    }

    public function testVerifyMockObjectsVerifiesWpOptions()
    {
        // Prepare
        $this->setExpectedException(
            '\WpMock\Exceptions\ExpectationFailedException'
        );
        WpOptions::expects('foo')->deleted();
        // Act
        $s = new WordPressTestCase();
        $s->verifyMockObjects();
    }
}
