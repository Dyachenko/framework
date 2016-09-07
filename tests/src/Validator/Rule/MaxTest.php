<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Max;

/**
 * Class MaxTest
 * @package Bluz\Tests\Validator\Rule
 */
class MaxTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidMaxInputShouldReturnTrue($maxValue, $inclusive, $input)
    {
        $max = new Max($maxValue, $inclusive);
        $this->assertTrue($max->validate($input));
        $this->assertNotEmpty($max->__toString());
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidMaxValueShouldReturnFalse($maxValue, $inclusive, $input)
    {
        $max = new Max($maxValue, $inclusive);
        $this->assertFalse($max->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [200, true, ''],     // empty string is equal zero
            [200, false, ''],    // empty string is equal zero
            [200, false, 165.0],
            [200, false, -200],
            [200, true, 200],
            [200, false, 0],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [200, false, 300],
            [200, false, 250],
            [200, false, 1500],
            [200, false, 200],
        );
    }
}
