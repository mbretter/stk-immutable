<?php

namespace StkTest\Immutable\Additions;

use PHPUnit\Framework\TestCase;
use Stk\Immutable\Map;
use Stk\Immutable\Util\Diff;


class DiffTest extends TestCase
{
    /** @var Diff */
    protected $util;

    public function setUp()
    {
        $this->util = new Diff();
    }

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

        $modified = $this->util->modified($m1, $m2);
        $this->assertEquals([], $modified->get());
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

        $modified = $this->util->modified($m1, $m2);
        $this->assertEquals(['b1' => 'bv2'], $modified->get());
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

        $modified = $this->util->modified($m1, $m2);
        $this->assertEquals([], $modified->get());
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

        $modified = $this->util->modified($m1, $m2);

        $expected = [
            'c1' => ['foo', 'bar'],
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expected, $modified->get());
    }

    // deleted
    public function testDeletedNone()
    {
        $m1 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $m2 = new Map([
            'a1' => 'av1',
            'b1' => 'bv1',
        ]);

        $deleted = $this->util->deleted($m1, $m2);

        $this->assertEquals([], $deleted->get());
    }

    public function testDeletedDiffers1()
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

        $deleted = $this->util->deleted($m1, $m2);

        $expected = [
            'c1' => ['foo', 'bar']
            ,
            'd1' => (object)[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ],
        ];

        $this->assertEquals($expected, $deleted->get());
    }

}