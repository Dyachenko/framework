<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Crud;

use Bluz\Proxy\Db;
use Bluz\Tests\Fixtures\Crud\TableCrud;
use Bluz\Tests\Fixtures\Models\Test\Row;
use Bluz\Tests\Fixtures\Models\Test\Table;
use Bluz\Tests\FrameworkTestCase;

/**
 * Crud TableTest
 *
 * @package  Bluz\Tests\Crud
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:13
 */
class TableTest extends FrameworkTestCase
{
    /**
     * @var TableCrud
     */
    protected $crudTable;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->crudTable = new TableCrud();
        $this->crudTable->setTable(Table::getInstance());

        Db::query(
            'INSERT INTO `test`(`id`, `name`, `email`, `status`) ' .
            'VALUES (100, "CrudTestTable", "table@test.com", "disable")'
        );
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        Db::query(
            'DELETE FROM `test` WHERE `name` = "CrudTestTable"'
        );
    }

    /**
     * getPrimaryKey Test
     */
    public function testGetPrimaryKey()
    {
        self::assertArraySubset(['id'], $this->crudTable->getPrimaryKey());
    }

    /**
     * Method readOne with empty $primary should return new instance of row
     */
    public function testReadOneCreate()
    {
        $row = $this->crudTable->readOne(null);

        self::assertInstanceOf(Row::class, $row);
    }

    /**
     * Method readOne with $primary should return instance of row
     */
    public function testReadOneWithCorrectPrimary()
    {
        $row = $this->crudTable->readOne(100);

        self::assertInstanceOf(Row::class, $row);
        self::assertEquals(100, $row->id);
    }

    /**
     * Method readOne with invalid $primary should throw exception
     *
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testReadOneWithInvalidPrimary()
    {
        $this->crudTable->readOne(10000);
    }

    /**
     * Method readSet should return array of rows
     */
    public function testReadSet()
    {
        $total = 0;
        $rows = $this->crudTable->readSet(0, 10, [], $total);
        self::assertCount(10, $rows);
        self::assertTrue($total > 0);
    }

    /**
     * Method CreateOne should return primary key
     */
    public function testCreateOne()
    {
        $result = $this->crudTable->createOne(
            [
                'name' => 'CrudTestTable',
                'email' => 'table@test.com',
                'status' => 'disabled'
            ]
        );
        self::assertArrayHasKey('id', $result);
    }

    /**
     * Method UpdateOne should return number of affected rows
     */
    public function testUpdateOne()
    {
        $result = $this->crudTable->updateOne(
            100,
            [
                'name' => 'CrudTestTable',
                'email' => uniqid('tableTest.', true) . '.' . date('His') . '@test.com',
                'status' => 'active'
            ]
        );

        self::assertEquals(1, $result);
    }

    /**
     * Method UpdateOne with invalid primary should throw exception
     *
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testUpdateOneWithInvalidPrimary()
    {
        $this->crudTable->updateOne(
            10000,
            [
                'name' => 'CrudTestTable',
                'email' => 'table@test.com',
                'status' => 'active'
            ]
        );
    }

    /**
     * Method DeleteOne with primary should delete one
     */
    public function testDeleteOneWithValidPrimary()
    {
        $result = $this->crudTable->deleteOne(100);
        self::assertEquals(1, $result);
    }

    /**
     * Method DeleteOne with invalid primary should throw exception
     *
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testDeleteOneWithInvalidPrimary()
    {
        $this->crudTable->deleteOne(10000);
    }
}
