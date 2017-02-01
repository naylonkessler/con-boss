<?php

namespace ConBoss\Test;

include_once 'mock/Some.php';
include_once 'mock/Another.php';
include_once 'mock/Main.php';
include_once 'mock/HasScalar.php';

use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    public function testClassExistence()
    {
        $this->assertTrue(class_exists('ConBoss\\Support\\Reflector'));
    }

    public function testClassInstance()
    {
        $reflector = new \ConBoss\Support\Reflector();

        $this->assertInstanceOf(\ConBoss\Support\Reflector::class, $reflector);

        return $reflector;
    }

    /**
     * @depends testClassInstance
     */
    public function testInfoOfExists($reflector)
    {
        $this->assertTrue(method_exists($reflector, 'infoOf'));

        return $reflector;
    }

    /**
     * @depends testInfoOfExists
     */
    public function testInfoOf($reflector)
    {
        $info = $reflector->infoOf('ConBoss\\Test\\Mock\\Main');

        $this->assertInstanceOf(\StdClass::class, $info);
        $this->assertObjectHasAttribute('dependencies', $info);
        $this->assertInternalType('array', $info->dependencies);
        $this->assertCount(2, $info->dependencies);

        return $reflector;
    }

    /**
     * @depends testInfoOfExists
     */
    public function testInfoOfWithScalar($reflector)
    {
        $info = $reflector->infoOf('ConBoss\\Test\\Mock\\HasScalar');

        $this->assertInstanceOf(\StdClass::class, $info);
        $this->assertObjectHasAttribute('dependencies', $info);
        $this->assertInternalType('array', $info->dependencies);
        $this->assertCount(3, $info->dependencies);
        $this->assertNull($info->dependencies[1]);
        $this->assertEquals(10, $info->dependencies[2]);

        return $reflector;
    }

    /**
     * @depends testInfoOf
     */
    public function testNewOfExists($reflector)
    {
        $this->assertTrue(method_exists($reflector, 'newOf'));

        return $reflector;
    }

    /**
     * @depends testNewOfExists
     */
    public function testNewOf($reflector)
    {
        $some = new \ConBoss\Test\Mock\Some();
        $another = new \ConBoss\Test\Mock\Another();

        $main = $reflector->newOf('ConBoss\\Test\\Mock\\Main', [$some, $another]);

        $this->assertInstanceOf(\ConBoss\Test\Mock\Main::class, $main);

        return $reflector;
    }
}