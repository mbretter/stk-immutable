<?php

namespace StkTest\Immutable;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stk\Immutable\Map;
use Stk\Immutable\MapInterface;
use Stk\Immutable\Record;

class MapTest extends TestCase
{

    public function testImmutability()
    {
        $a = new Map((object)['x' => 'foo', 'y' => 'bar']);
        $b = $a->set('x', 'whatever');

        $this->assertNotSame($a, $b);

        $b = $a->del('x');
        $this->assertNotSame($a, $b);
    }

    public function testWithMutations()
    {
        $x = $a = new Map((object)['x' => 'foo', 'y' => 'bar']);

        $b = $a->withMutations(function (MapInterface $a) use (&$b) {
            $a->set('x', 'whatever');
            $a->del('y');
        });

        $this->assertSame($a, $x);
        $this->assertEquals((object)['x' => 'whatever'], $b->get());
    }

    public function testClone()
    {
        $a = new Map((object)['x' => 'foo', 'y' => 'bar']);
        $b = $a->set('x', 'whatever');

        $this->assertEquals((object)['x' => 'foo', 'y' => 'bar'], $a->get());
        $this->assertEquals((object)['x' => 'whatever', 'y' => 'bar'], $b->get());

        // test with nested objects
        $a = new Map((object)['x' => (object)['y' => 'bar']]);
        $b = $a->setIn(['x', 'y'], 'whatever');

        $this->assertEquals((object)['x' => (object)['y' => 'bar']], $a->get());
        $this->assertEquals((object)['x' => (object)['y' => 'whatever']], $b->get());
    }

    // set

    public function testSetScalar()
    {
        $a = new Map();
        $b = $a->set('x', 1);
        $this->assertEquals((object)['x' => 1], $b->get());
    }

    public function testSetObjectUsingArray()
    {
        $a = new Map();
        $b = $a->setIn(['x'], 1);
        $this->assertEquals((object)['x' => 1], $b->get());
    }

    public function testSetNull()
    {
        $a = new Map((object)['x' => 'foo', 'y' => 'bar']);
        $b = $a->set(null);

        $this->assertEquals((object)['x' => 'foo', 'y' => 'bar'], $a->get());
        $this->assertEquals(null, $b->get());

        $c = $b->set('foo', 'bar');
        $this->assertEquals((object)['x' => 'foo', 'y' => 'bar'], $a->get());
        $this->assertEquals(null, $b->get());
        $this->assertEquals((object)['foo' => 'bar'], $c->get());
    }

    public function testSetData()
    {
        $a = new Map();
        $b = $a->set(['x' => 'xyz']);
        $this->assertEquals(['x' => 'xyz'], $b->get());
    }

    /**
     * {x:{y:1}}
     */
    public function testSetObjectsUsingArrayPaths()
    {
        $a = new Map();
        $b = $a->setIn(['x', 'y'], 1);
        $this->assertEquals((object)['x' => (object)['y' => 1]], $b->get());
    }

    public function testSetArrayUsingArray()
    {
        $a = new Map([]);
        $b = $a->setIn(['x'], 1);
        $this->assertEquals(['x' => 1], $b->get());
    }

    /**
     * {x:[]}
     * {x:[42]}
     */
    public function testSetArrayIntoObject()
    {
        $a = new Map();
        $b = $a->setIn(['x'], []);
        $c = $b->setIn(['x', 0], 42);

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
        $a = new Map();
        $b = $a->setIn(['x'], []);
        $c = $b->setIn(['x', 0], (object)['a' => 42]);
        $d = $c->setIn(['x', 1], (object)['b' => 24]);

        $this->assertEquals((object)['x' => []], $b->get());
        $this->assertEquals((object)['x' => [(object)['a' => 42]]], $c->get());
        $this->assertEquals((object)['x' => [(object)['a' => 42], (object)['b' => 24]]], $d->get());
    }

    public function testSetInArray()
    {
        $data = ['x' => [['a' => 42], ['b' => 24]]];
        $a = new Map($data);

        $expected1 = $expected2 = $data;
        $expected1['x'][0]['foo'] = 'bar';
        $this->assertEquals($expected1, $a->setIn(['x', 0, 'foo'], 'bar')->get());

        $expected2['z'] = [['foo' => 'bar']];
        $this->assertEquals($expected2, $a->setIn(['z', 0, 'foo'], 'bar')->get());
    }

    public function testSetWithInsufficientParams()
    {
        $a = new Map(['x' => 'foo', 'y' => 'bar']);

        $this->assertSame($a, $a->set());
    }

    // get

    public function testGetFromObjects()
    {
        $a = new Map((object)['b' => 24, 'c' => (object)['d' => 42]]);

        $this->assertEquals(24, $a->get('b'));
        $this->assertEquals(42, $a->get('c', 'd'));
        $this->assertNull($a->get('x'));
        $this->assertNull($a->get('c', 'x'));
        $this->assertNull($a->get('c', 'd', 'x'));
    }

    public function testGetFromArray()
    {
        $a = new Map(['b' => 24, 'c' => ['d' => 42]]);

        $this->assertEquals(24, $a->get('b'));
        $this->assertEquals(42, $a->get('c', 'd'));
        $this->assertNull($a->get('x'));
        $this->assertNull($a->get('c', 'x'));
        $this->assertNull($a->get('c', 'd', 'x'));
    }

    public function testGetObject()
    {
        $o = (object)['d' => 42];
        $a = new Map((object)['b' => 24, 'c' => $o]);

        // must be a clonse
        $this->assertNotSame($o, $a->get('c'));
    }

    public function testGetInWithEmptyPath()
    {
        $data = (object)['b' => 24, 'c' => (object)['d' => 42]];
        $a = new Map($data);

        // must be a clonse
        $this->assertEquals($data, $a->getIn([]));
    }

    // has

    public function testHasInObject()
    {
        $a = new Map((object)['b' => 24, 'c' => (object)['d' => 42]]);

        $this->assertTrue($a->hasIn(['c', 'd']));
        $this->assertFalse($a->hasIn(['foo']));
        $this->assertTrue($a->hasIn([]));
    }

    public function testHasInArray()
    {
        $a = new Map(['b' => 24, 'c' => (object)['d' => 42]]);

        $this->assertTrue($a->hasIn(['c', 'd']));
        $this->assertFalse($a->hasIn(['foo']));
    }


    // delete

    /**
     * {b:24,c:{d:42}}
     * {b:24,c:[42]}
     */
    public function testDelObject()
    {
        $a = new Map((object)['b' => 24, 'c' => (object)['d' => 42]]);

        $b = $a->del('c');
        $this->assertEquals((object)['b' => 24, 'c' => (object)['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24], $b->get());

        $c = $a->del('c', 'd');
        $this->assertEquals((object)['b' => 24, 'c' => (object)['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24, 'c' => (object)[]], $c->get());

        $a = new Map((object)['b' => 24, 'c' => [42]]);
        $d = $a->del('c', 0);
        $this->assertEquals((object)['b' => 24, 'c' => [42]], $a->get()); // orig object must not mutate
        $this->assertEquals((object)['b' => 24, 'c' => []], $d->get());
    }

    /**
     * {b:24,c:{d:42}}
     * {b:24,c:[42]}
     */
    public function testDelArray()
    {
        $a = new Map(['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->del('c');
        $this->assertEquals(['b' => 24, 'c' => ['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals(['b' => 24], $b->get());

        $c = $a->del('c', 'd');
        $this->assertEquals(['b' => 24, 'c' => ['d' => 42]], $a->get()); // orig object must not mutate
        $this->assertEquals(['b' => 24, 'c' => []], $c->get());
    }

    public function testDelAllArray()
    {
        $a = new Map(['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->del();
        $this->assertEquals([], $b->get());
    }

    public function testDelInAllObject()
    {
        $a = new Map((object)['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->delIn([]);
        $this->assertEquals(new stdClass(), $b->get()); // orig object must not mutate
    }

    public function testDelInObjectNotFound()
    {
        $a = new Map((object)['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->delIn(['x']);
        $this->assertEquals($b, $a);
        $this->assertNotSame($b, $a);
    }

    public function testDelInArrayNotFound()
    {
        $a = new Map(['b' => 24, 'c' => ['d' => 42]]);

        $b = $a->delIn(['x']);
        $this->assertEquals($b, $a);
        $this->assertNotSame($b, $a);
    }

    // walk

    public function testWalk()
    {
        $m1 = new Map([
            'a' => 'av1',
            'b' => 'bv1',
            'c' => ['foo', 'bar'],
            'd' => (object)['d1' => 'val1', 'd2' => 'val2'],
            'e' => (object)['e1' => 'val1', 'e2' => ['v1', 'v2', null]],
            'f' => (object)['f1' => 'val1', 'f2' => ['v1', ['x1' => 'y1', 'x2' => 'y2'], null]]
        ]);

        $str = '';
        $m1->walk(function ($path, $value) use (&$str) {
            $str .= sprintf("%s:%s\n", implode(",", $path), is_null($value) ? '-null-' : $value);
        });

        $this->assertEquals("a:av1
b:bv1
c,0:foo
c,1:bar
d,d1:val1
d,d2:val2
e,e1:val1
e,e2,0:v1
e,e2,1:v2
e,e2,2:-null-
f,f1:val1
f,f2,0:v1
f,f2,1,x1:y1
f,f2,1,x2:y2
f,f2,2:-null-
", $str);
    }
}