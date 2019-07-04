<?php

namespace StkTest\Immutable\Ops;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\Map;
use Stk\Immutable\Ops\Diff;

class DiffTest extends TestCase
{
    // modified
    public function testModifiedSimpleEquals()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $this->assertEquals([], (new Diff)($m1, $m2)->get());
    }

    public function testModifiedSimpleDiffers()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv2',
        ]);

        $this->assertEquals(['b1' => 'bv2'], (new Diff)($m1, $m2)->get());
    }

    public function testModifiedWithDeleted()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
            'c1' => ['foo', 'bar'],
            'd1' => (object)['prop1' => 'val1', 'prop2' => 'val2']
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $expected = [
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals([], (new Diff)($m1, $m2)->get());
    }

    public function testModifiedComplex1()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
            'c1' => ['foo', 'bar'],
            'd1' => (object)['prop1' => 'val1', 'prop2' => 'val2']
        ]);

        $expected = [
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expected, (new Diff)($m1, $m2)->get());
    }

    public function testDeletedDiffers1()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
            'd1' => (object)['prop1' => 'val1', 'prop2' => 'val2']
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
            'c1' => ['foo', 'rab'],
        ]);

        $expectedModified = [
            'c1' => ['foo', 'rab'],
        ];

        $this->assertEquals($expectedModified, (new Diff)($m1, $m2)->get());
    }

}