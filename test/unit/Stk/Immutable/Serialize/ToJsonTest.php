<?php

namespace StkTest\Immutable\Serialize;

use PHPUnit\Framework\TestCase;

use Stk\Immutable\Immutable;
use Stk\Immutable\Serialize\ToJson;

class MyJsonMap
{
    use Immutable;
    use ToJson;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    public function toJson()
    {
        return $this->_dataToJson($this->_data);
    }
}

class ToJsonTest extends TestCase
{
    public function testObject()
    {
        $a = new MyJsonMap((object)['x' => 'foo', 'y' => 'bar']);
        $this->assertEquals('{"x":"foo","y":"bar"}', $a->toJson());
    }
}