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