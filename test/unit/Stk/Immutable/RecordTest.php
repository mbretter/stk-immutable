<?php

namespace StkTest\Immutable;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\ImmutableInterface;
use Stk\Immutable\Record;


class RecordTest extends TestCase
{
    public function testImmutability()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);
        $b = $a->set('x', 'whatever');

        $this->assertNotSame($a, $b);

        $b = $a->del('x');
        $this->assertNotSame($a, $b);
    }

    public function testWithMutations()
    {
        $x = $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $b = $a->withMutations(function (ImmutableInterface $a) use (&$b) {
            $a->set('x', 'whatever');
            $a->del('y');
        });

        $this->assertSame($a, $x);
        $this->assertEquals(['x' => 'whatever'], $b->get());
    }

    public function testGet()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $this->assertEquals('foo', $a->get('x'));
    }

    public function testSetWithInsufficientParams()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $this->assertSame($a, $a->set());
    }

    public function testSetData()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $b = $a->set(['a' => 'abc']);
        $this->assertNotSame($a, $b);

        $this->assertEquals(['a' => 'abc'], $b->get());
    }

    public function testHas()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $this->assertFalse($a->has('alice'));
        $this->assertTrue($a->has('x'));
    }

    public function testHasWithInsufficientParams()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);

        $this->assertFalse($a->has());
    }

    public function testClone()
    {
        $a = new Record(['x' => 'foo', 'y' => 'bar']);
        $b = $a->set('x', 'whatever');

        $this->assertEquals(['x' => 'foo', 'y' => 'bar'], $a->get());
        $this->assertEquals(['x' => 'whatever', 'y' => 'bar'], $b->get());
    }

    public function testWalk()
    {
        $m1 = new Record([
            'a' => 'av1',
            'b' => 'bv1',
            'c' => ['foo', 'bar'],
            'd' => ['d1' => 'val1', 'd2' => 'val2']
        ]);

        $str = '';
        $m1->walk(function ($key, $value) use (&$str) {
            $str .= sprintf("%s:%s\n", $key, is_null($value) ? '-null-' : (is_array($value) ? '-array-' : $value));
        });

        $this->assertEquals("a:av1
b:bv1
c:-array-
d:-array-
", $str);
    }
}