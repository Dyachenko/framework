<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid;

/**
 * Grid Data
 *
 * @package  Bluz\Grid
 * @author   AntonShevchuk
 */
class Data extends \ArrayIterator
{
    /**
     * @var integer how many data rows w/out limits
     */
    protected $total;

    /**
     * Set total rows
     *
     * @param  integer $total
     *
     * @return self
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Get total rows
     *
     * @return integer
     */
    public function getTotal(): int
    {
        return $this->total;
    }
}
