<?php

namespace ConBoss\Test;

include_once 'mock/Some.php';
include_once 'mock/Another.php';
include_once 'mock/Main.php';
include_once 'mock/Unbound.php';
include_once 'mock/LevelTop.php';
include_once 'mock/LevelSecond.php';
include_once 'mock/LevelThird.php';
include_once 'mock/AnInterface.php';
include_once 'mock/HasScalar.php';
include_once 'mock/NestedHasScalar.php';

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testClassExistence()
    {
        $this->assertTrue(class_exists('ConBoss\\Container'));
    }

    public function testClassInstance()
    {
        $reflector = new \ConBoss\Support\Reflector();
        $resolver = new \ConBoss\Support\Resolver($reflector);
        $container = new \ConBoss\Container($resolver);

        $this->assertInstanceOf(\ConBoss\Container::class, $container);

        return $container;
    }

    public function testClassInstanceWithoutDependencies()
    {
        $container = new \ConBoss\Container();

        $this->assertInstanceOf(\ConBoss\Container::class, $container);

        return $container;
    }

    /**
     * @depends testClassInstance
     */
    public function testBindExists($container)
    {
        $this->assertTrue(method_exists($container, 'bind'));

        return $container;
    }

    /**
     * @depends testBindExists
     */
    public function testBind($container)
    {
        $result = $container->bind('some', 'Some');

        $this->assertInstanceOf(\ConBoss\Container::class, $result);

        return $container;
    }

    /**
     * @depends testBind
     */
    public function testHasExists($container)
    {
        $this->assertTrue(method_exists($container, 'has'));

        return $container;
    }

    /**
     * @depends testHasExists
     */
    public function testHas($container)
    {
        $this->assertTrue($container->has('some'));

        return $container;
    }

    /**
     * @depends testHas
     */
    public function testShareExists($container)
    {
        $this->assertTrue(method_exists($container, 'share'));

        return $container;
    }

    /**
     * @depends testShareExists
     */
    public function testShare($container)
    {
        $result = $container->share('another', 'Another');

        $this->assertInstanceOf(\ConBoss\Container::class, $result);

        return $container;
    }

    /**
     * @depends testShare
     */
    public function testGetExists($container)
    {
        $this->assertTrue(method_exists($container, 'get'));

        return $container;
    }

    /**
     * @depends testGetExists
     */
    public function testGetFromStringBind($container)
    {
        $container->bind('some', \ConBoss\Test\Mock\Some::class);

        $some = $container->get('some');

        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $some);

        return $container;
    }

    /**
     * @depends testGetFromStringBind
     */
    public function testGetFromCallableBind($container)
    {
        $container->bind('another', function ($container)
        {
            return new \ConBoss\Test\Mock\Another();
        });

        $another = $container->get('another');

        $this->assertInstanceOf(\ConBoss\Test\Mock\Another::class, $another);

        return $container;
    }

    /**
     * @depends testGetFromCallableBind
     */
    public function testGetWithDependencies($container)
    {
        $container->bind('main', \ConBoss\Test\Mock\Main::class);

        $main = $container->get('main');

        $this->assertInstanceOf(\ConBoss\Test\Mock\Main::class, $main);
        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $main->some);
        $this->assertInstanceOf(\ConBoss\Test\Mock\Another::class, $main->another);

        return $container;
    }

    /**
     * @depends testGetWithDependencies
     */
    public function testGetShared($container)
    {
        $container->share('some', \ConBoss\Test\Mock\Some::class);

        $some1 = $container->get('some');
        $some2 = $container->get('some');

        $this->assertTrue($some1 === $some2);

        return $container;
    }

    /**
     * @depends testGetShared
     */
    public function testUnboundInstance($container)
    {
        $unbound = $container->get(\ConBoss\Test\Mock\Unbound::class);

        $this->assertInstanceOf(\ConBoss\Test\Mock\Unbound::class, $unbound);

        return $container;
    }

    /**
     * @depends testUnboundInstance
     */
    public function testGetMultiple($container)
    {
        list($some, $another) = $container->get(['some', 'another']);

        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $some);
        $this->assertInstanceOf(\ConBoss\Test\Mock\Another::class, $another);

        return $container;
    }

    /**
     * @depends testGetMultiple
     */
    public function testGetWithDeepDependencies($container)
    {
        $container->bind(\ConBoss\Test\Mock\LevelThird::class, function ($container)
        {
            return new \ConBoss\Test\Mock\LevelThird('a fourth');
        });

        $top = $container->get(\ConBoss\Test\Mock\LevelTop::class);

        $this->assertInstanceOf(\ConBoss\Test\Mock\LevelTop::class, $top);
        $this->assertInstanceOf(\ConBoss\Test\Mock\LevelSecond::class, $top->second);
        $this->assertInstanceOf(\ConBoss\Test\Mock\LevelThird::class, $top->second->third);

        return $container;
    }

    /**
     * @depends testGetWithDeepDependencies
     */
    public function testGetFromInterface($container)
    {
        $container->bind(\ConBoss\Test\Mock\AnInterface::class, \ConBoss\Test\Mock\LevelTop::class);

        $top = $container->get(\ConBoss\Test\Mock\AnInterface::class);

        $this->assertInstanceOf(\ConBoss\Test\Mock\LevelTop::class, $top);

        return $container;
    }

    /**
     * @depends testGetExists
     */
    public function testGetWithScalarDependencies($container)
    {
        $container->bind('scalar', \ConBoss\Test\Mock\HasScalar::class);

        $scalar = $container->get('scalar');

        $this->assertInstanceOf(\ConBoss\Test\Mock\HasScalar::class, $scalar);
        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $scalar->some);
        $this->assertNull($scalar->scalar);
        $this->assertEquals(10, $scalar->defaults);

        return $container;
    }

    /**
     * @depends testGetWithScalarDependencies
     */
    public function testGetWithNestedScalarDependencies($container)
    {
        $top = $container->get(\ConBoss\Test\Mock\NestedHasScalar::class);

        $this->assertInstanceOf(\ConBoss\Test\Mock\NestedHasScalar::class, $top);
        $this->assertInstanceOf(\ConBoss\Test\Mock\HasScalar::class, $top->hasScalar);
        $this->assertInstanceOf(\ConBoss\Test\Mock\Another::class, $top->another);
        $this->assertNull($top->hasScalar->scalar);
        $this->assertEquals(10, $top->hasScalar->defaults);

        return $container;
    }
}