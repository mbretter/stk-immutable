<?php

namespace Stk\Immutable;

use Closure;
use stdClass;

class Map implements MapInterface
{
    use Immutable;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    public function walk(Closure $callback)
    {
        $this->_walk($this->_data, $callback);
    }

    protected function _walk($a, Closure $callback)
    {
        static $path = [];

        foreach ($a as $k => $v) {
            $path[] = $k;
            if (is_array($v) || $v instanceof stdClass) {
                $this->_walk($v, $callback);
            } else {
                $callback($path, $v);
            }
            array_pop($path);
        }
    }

}
