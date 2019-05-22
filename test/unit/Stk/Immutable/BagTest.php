<?php

namespace StkTest\Immutable;

use Stk\Immutable\Bag;

class BagTest extends Base
{

    public function setUp()
    {

    }

    public function testClone()
    {
        $a = new Bag((object)['x' => 'foo', 'y' => 'bar']);
        $b = $a->set('x', 'whatever');

        $this->assertEquals((object)['x' => 'foo', 'y' => 'bar'], $a->get());
        $this->assertEquals((object)['x' => 'whatever', 'y' => 'bar'], $b->get());

        // test with nested objects
        $a = new Bag((object)['x' => (object)['y' => 'bar']]);
        $b = $a->set(['x', 'y'], 'whatever');

        $this->assertEquals((object)['x' => (object)['y' => 'bar']], $a->get());
        $this->assertEquals((object)['x' => (object)['y' => 'whatever']], $b->get());
    }

    // set

    public function testSetScalar()
    {
        $a = new Bag();
        $b = $a->set('x', 1);
        $this->assertEquals((object)['x' => 1], $b->get());
    }

    public function testSetObjectUsingArray()
    {
        $a = new Bag();
        $b = $a->set(['x'], 1);
        $this->assertEquals((object)['x' => 1], $b->get());
    }

    /**
     * {x:{y:1}}
     */
    public function testSetObjectsUsingArrayPaths()
    {
        $a = new Bag();
        $b = $a->set(['x', 'y'], 1);
        $this->assertEquals((object)['x' => (object)['y' => 1]], $b->get());
    }

    public function testSetArrayUsingArray()
    {
        $a = new Bag([]);
        $b = $a->set(['x'], 1);
        $this->assertEquals(['x' => 1], $b->get());
    }

    /**
     * {x:[]}
     * {x:[42]}
     */
    public function testSetArrayIntoObject()
    {
        $a = new Bag();
        $b = $a->set(['x'], []);
        $c = $b->set(['x', 0], 42);

        $this->assertEquals((object)['x' => []], $b->get());
        $this->assertEquals((object)['x' => [42]], $c->get());
    }

    /**
     * {x:[]}
     * {x:[{a:42}]}
     * {x:[{a:42},{b:24}]}
     */
    public function testSetNestedArrayOfObjects()
    {
        $a = new Bag();
        $b = $a->set(['x'], []);
        $c = $b->set(['x', 0], (object)['a' => 42]);
        $d = $c->set(['x', 1], (object)['b' => 24]);

        $this->assertEquals((object)['x' => []], $b->get());
        $this->assertEquals((object)['x' => [(object)['a' => 42]]], $c->get());
        $this->assertEquals((object)['x' => [(object)['a' => 42], (object)['b' => 24]]], $d->get());
    }

    // get

    public function testGet()
    {
        $a = new Bag((object)['b' => 24, 'c' => (object)['d' => 42]]);

        $this->assertEquals(24, $a->get('b'));
        $this->assertEquals(42, $a->get('c', 'd'));
        $this->assertNull($a->get('x'));
        $this->assertNull($a->get('c', 'x'));
        $this->assertNull($a->get('c', 'd', 'x'));
    }

    // delete

    /**
     * {b:24,c:{d:42}}
     * {b:24,c:[42]}
     */
    public function testDeleteObject()
    {
        $a = new Bag((object)['b' => 24, 'c' => (object)['d' => 42]]);

        $b = $a->delete('c');
        $this->assertEquals((object)['b' => 24, 'c' => (object)['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24], $b->get());

        $c = $a->delete('c', 'd');
        $this->assertEquals((object)['b' => 24, 'c' => (object)['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24, 'c' => (object)[]], $c->get());

        $a = new Bag((object)['b' => 24, 'c' => [42]]);
        $d = $a->delete('c', 0);
        $this->assertEquals((object)['b' => 24, 'c' => [42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24, 'c' => []], $d->get());
    }

    /**
     * {b:24,c:{d:42}}
     * {b:24,c:[42]}
     */
    public function testDeleteArray()
    {
        $a = new Bag(['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->delete('c');
        $this->assertEquals(['b' => 24, 'c' => ['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals(['b' => 24], $b->get());

        $c = $a->delete('c', 'd');
        $this->assertEquals(['b' => 24, 'c' => ['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals(['b' => 24, 'c' => []], $c->get());
    }
}