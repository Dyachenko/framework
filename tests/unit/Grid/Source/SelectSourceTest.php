<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Grid\Source;

use Bluz\Grid\Grid;
use Bluz\Grid\Source\SelectSource;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Grid\SelectGrid;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class SelectSourceTest extends FrameworkTestCase
{
    /**
     * Select Source
     */
    public function testSelectGrid()
    {
        $grid = new SelectGrid();
        $grid->setDefaultOrder('id', Grid::ORDER_DESC);
        $grid->addFilter('id', Grid::FILTER_GT, 1);  // id > 1
        $grid->addFilter('email', Grid::FILTER_LIKE, '@');
        self::assertEquals(5, $grid->pages());
        self::assertEquals(41, $grid->total());
    }

    /**
     * Select Source Exception
     *
     * @expectedException \Bluz\Grid\GridException
     */
    public function testSelectSourceThrowsGridException()
    {
        $adapter = new SelectSource();
        $adapter->setSource('wrong source type');
    }
}
