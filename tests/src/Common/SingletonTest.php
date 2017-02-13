<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common;

use Bluz\Tests\TestCase;
use Bluz\Tests\Common\Fixtures\ConcreteSingleton;

/**
 * SingletonTest
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 13:24
 */
class SingletonTest extends TestCase
{
    /**
     * Test GetInstance
     */
    public function testGetInstance()
    {
        $result = ConcreteSingleton::getInstance();
        $result->foo = 'bar';

        self::assertInstanceOf('Bluz\Tests\Common\Fixtures\ConcreteSingleton', $result);
        self::assertEquals(ConcreteSingleton::getInstance(), $result);
        self::assertEquals('bar', ConcreteSingleton::getInstance()->foo);
    }

    /**
     * Test Clone
     */
    public function testPrivateMethods()
    {
        $result = ConcreteSingleton::getInstance();

        $reflection = new \ReflectionObject($result);

        self::assertTrue($reflection->getMethod('__construct')->isPrivate());
        self::assertTrue($reflection->getMethod('__clone')->isPrivate());
        self::assertTrue($reflection->getMethod('__wakeup')->isPrivate());
    }
}
