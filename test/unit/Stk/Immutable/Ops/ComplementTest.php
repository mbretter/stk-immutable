<?php

namespace StkTest\Immutable\Ops;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\Map;
use Stk\Immutable\Ops\Complement;

class ComplementTest extends TestCase
{
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

        $this->assertEquals([], (new Complement())($m1, $m2)->get());
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

        $this->assertEquals([], (new Complement())($m1, $m2)->get());
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

        $this->assertEquals($expected, (new Complement())($m1, $m2)->get());
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

        $this->assertEquals([], (new Complement())($m1, $m2)->get());
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

        $expectedDeleted = [
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expectedDeleted, (new Complement())($m1, $m2)->get());
    }

}