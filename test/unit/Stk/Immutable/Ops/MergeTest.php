<?php

namespace StkTest\Immutable\Ops;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\Map;
use Stk\Immutable\Ops\Merge;

class MergeTest extends TestCase
{
    public function testMergeSimple()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $m2 = new Map([
            'c1' => 'cv1',
        ]);

        $expected = [
            'a1' => 'av1',
            'b1' => 'bv1',
            'c1' => 'cv1',
        ];
        $this->assertEquals($expected, (new Merge)($m1, $m2)->get());
    }

    public function testMergeNested()
    {
        $m1 = new Map([
            'c1' => ['foo', 'bar'],
            'd1' => (object)['prop1' => 'val1', 'prop2' => 'val2']
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'd1' => (object)['prop1' => 'val1', 'prop2' => 'val4']
        ]);

        $expected = [
            'a1' => 'av1',
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val4',
            ],
        ];

        $this->assertEquals($expected, (new Merge())($m1, $m2)->get());
    }

    public function testMergeComplex()
    {
        $m1 = new Map([
            'd1' => (object)['prop1' => ['prop1' => (object)['prop1' => 'val1'], 'prop2' => 'val2']]
        ]);

        $m2 = new Map([
            'd1' => (object)['prop1' => ['prop1' => (object)['prop1' => 'val2']]]
        ]);

        $expected = [
            'd1' => (object)['prop1' => ['prop1' => (object)['prop1' => 'val2'], 'prop2' => 'val2']]
        ];

        $this->assertEquals($expected, (new Merge())($m1, $m2)->get());
    }

}