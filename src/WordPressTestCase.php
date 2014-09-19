<?php

namespace WpMock;

class WordPressTestCase extends \PHPUnit_Framework_TestCase
{
    // Hook into setUp
    public function setUp()
    {
        parent::setUp();
        WpOptions::reset();
    }

    // Hook into verify
    public function verifyMockObjects()
    {
        parent::verifyMockObjects();
        WpOptions::verify();
    }
}
