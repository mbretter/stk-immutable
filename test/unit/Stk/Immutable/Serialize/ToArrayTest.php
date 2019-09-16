<?php

namespace StkTest\Immutable\Serialize;

use PHPUnit\Framework\TestCase;

use stdClass;
use Stk\Immutable\Serialize\ToArray;
use Stk\Immutable\Immutable;

class MyMap
{
    use Immutable;
    use ToArray;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    public function toArray()
    {
        return $this->_dataToArray($this->_data);
    }
}

class ToArrayTest extends TestCase
{

    public function testObject()
    {
        $a = new MyMap((object)['x' => 'foo', 'y' => 'bar']);
        $this->assertEquals([
            'x' => 'foo',
            'y' => 'bar',
        ], $a->toArray());
    }

    public function testWithEmptyStdClass()
    {
        $a = new MyMap((object)['x' => 'foo', 'y' => new stdClass()]);
        $this->assertEquals([
            'x' => 'foo',
            'y' => new stdClass(),
        ], $a->toArray());
    }

    public function testWithNonEmptyStdClass()
    {
        $a = new MyMap((object)['x' => 'foo', 'y' => (object)['a' => 1, 'b' => 2]]);
        $this->assertEquals([
            'x' => 'foo',
            'y' => [
                'a' => 1,
                'b' => 2
            ]
        ], $a->toArray());
    }

    public function testWithNullValues()
    {
        $a = new MyMap((object)['x' => 'foo', 'y' => new stdClass(), 'z' => null]);
        $this->assertEquals([
            'x' => 'foo',
            'y' => new stdClass(),
            'z' => null
        ], $a->toArray());
    }

    public function testObjectWithArray()
    {
        $a = new MyMap((object)[
            'x' => 'foo',
            'y' => [
                (object)['subid' => 1, 'b' => true],
                (object)['subid' => 2, 'b' => false],
            ],
            'z' => [],
        ]);
        $this->assertEquals([
            'x' => 'foo',
            'y' => [
                ['subid' => 1, 'b' => true],
                ['subid' => 2, 'b' => false]
            ],
            'z' => []
        ], $a->toArray());
    }
}