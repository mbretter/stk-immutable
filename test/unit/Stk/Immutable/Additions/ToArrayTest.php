<?php

namespace StkTest\Immutable\Additions;

use PHPUnit\Framework\TestCase;

use Stk\Immutable\Additions\ToArray;
use Stk\Immutable\Immutable;

class MyBag
{
    use Immutable;
    use ToArray;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    public function toArray()
    {
        return $this->dataToArray($this->_data);
    }
}

class ToArrayTest extends TestCase
{

    public function setUp()
    {

    }

    public function testToArray()
    {
        $a = new MyBag((object)['x' => 'foo', 'y' => 'bar']);
        $this->assertEquals(array(
            'x' => 'foo',
            'y' => 'bar',
        ), $a->toArray());
    }


    public function testToArrayWithEmptyStdClass()
    {
        $a = new MyBag((object)['x' => 'foo', 'y' => new \stdClass()]);
        $this->assertEquals(array(
            'x' => 'foo',
            'y' => new \stdClass(),
        ), $a->toArray());
    }

    public function testToArrayWithNonEmptyStdClass()
    {
        $a = new MyBag((object)['x' => 'foo', 'y' => (object)['a' => 1, 'b' => 2]]);
        $this->assertEquals(array(
            'x' => 'foo',
            'y' => [
                'a' => 1,
                'b' => 2
            ]
        ), $a->toArray());
    }
}