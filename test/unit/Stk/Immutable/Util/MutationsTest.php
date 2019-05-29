<?php

namespace StkTest\Immutable\Additions;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\Map;
use Stk\Immutable\Util\Mutations;

class MutationsTest extends TestCase
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

        $mutations = new Mutations($m1, $m2);
        $this->assertEquals([], $mutations->getModified()->get());
        $this->assertEquals([], $mutations->getDeleted()->get());
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

        $mutations = new Mutations($m1, $m2);
        $this->assertEquals(['b1' => 'bv2'], $mutations->getModified()->get());
        $this->assertEquals([], $mutations->getDeleted()->get());

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

        $mutations = new Mutations($m1, $m2);

        $expected = [
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expected, $mutations->getDeleted()->get());
        $this->assertEquals([], $mutations->getModified()->get());
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

        $mutations = new Mutations($m1, $m2);

        $expected = [
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expected, $mutations->getModified()->get());
        $this->assertEquals([], $mutations->getDeleted()->get());
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

        $mutations = new Mutations($m1, $m2);

        $expectedModified = [
            'c1' => ['foo', 'rab'],
        ];

        $expectedDeleted = [
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expectedModified, $mutations->getModified()->get());
        $this->assertEquals($expectedDeleted, $mutations->getDeleted()->get());
    }

}