<?php

require_once 'include.php';

use PHPUnit\Framework\TestCase;

class InitTest extends TestCase {

    public function testInit() {

        $this->assertSame('1','1');
    }

}