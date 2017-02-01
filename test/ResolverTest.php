<?php

namespace ConBoss\Test;

include_once 'mock/Some.php';

use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public function testClassExistence()
    {
        $this->assertTrue(class_exists('ConBoss\\Support\\Resolver'));
    }

    public function testClassInstance()
    {
        $resolver = new \ConBoss\Support\Resolver(new \ConBoss\Support\Reflector());

        $this->assertInstanceOf(\ConBoss\Support\Resolver::class, $resolver);

        return $resolver;
    }

    public function testClassInstanceWithoutDependencies()
    {
        $resolver = new \ConBoss\Support\Resolver();

        $this->assertInstanceOf(\ConBoss\Support\Resolver::class, $resolver);

        return $resolver;
    }

    /**
     * @depends testClassInstance
     */
    public function testResolveExists($resolver)
    {
        $this->assertTrue(method_exists($resolver, 'resolve'));

        return $resolver;
    }

    /**
     * @depends testResolveExists
     */
    public function testResolveStringTarget($resolver)
    {
        $container = new \ConBoss\Container($resolver);
        $some = $resolver->resolve('ConBoss\\Test\\Mock\\Some', $container);

        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $some);

        return $resolver;
    }

    /**
     * @depends testResolveExists
     */
    public function testResolveCallableTarget($resolver)
    {
        $container = new \ConBoss\Container($resolver);
        $some = $resolver->resolve(function (\ConBoss\Container $container)
        {
            return new \ConBoss\Test\Mock\Some();
        }, $container);

        $this->assertInstanceOf(\ConBoss\Test\Mock\Some::class, $some);

        return $resolver;
    }

    /**
     * @depends testResolveExists
     */
    public function testResolveOthersTarget($resolver)
    {
        $container = new \ConBoss\Container($resolver);
        $null = $resolver->resolve(null, $container);

        $this->assertNull($null);

        $number = $resolver->resolve(10, $container);

        $this->assertEquals(10, $number);

        return $resolver;
    }
}