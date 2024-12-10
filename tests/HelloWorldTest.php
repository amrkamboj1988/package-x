<?php

use PHPUnit\Framework\TestCase;
use Amie\PackageX\HelloWorld;

class HelloWorldTest extends TestCase
{
    public function testSayHello()
    {
        $helloWorld = new HelloWorld();
        $this->assertEquals('Hello, World!', $helloWorld->sayHello());
    }
}
