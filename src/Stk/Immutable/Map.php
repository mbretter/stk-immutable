<?php

namespace Stk\Immutable;

use Closure;
use stdClass;

class Map implements MapInterface
{
    use Immutable;
    use Methods\ReadIn;
    use Methods\WriteIn;
    use Methods\DeleteIn;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    public function get(...$args)
    {
        return $this->getIn($args);
    }

    public function getIn(array $path)
    {
        if (count($path) === 0) {
            return is_object($this->_data) ? clone($this->_data) : $this->_data;
        }

        $elem = $this->_getIn($this->_data, $path);

        if (is_object($elem)) {
            return clone($elem);
        } else {
            return $elem;
        }
    }

    public function set(...$args): MapInterface
    {
        if (count($args) < 1) {
            return $this;
        }

        $val = array_pop($args);

        return $this->setIn($args, $val);
    }

    public function setIn(array $path, $val): MapInterface
    {
        /** @var Map $c */
        $c = $this->getClone();

        $c->_setIn($c->_data, $path, $val);

        return $c;
    }

    /**
     * @param mixed ...$args
     *
     * @return MapInterface
     */
    public function del(...$args): MapInterface
    {
        return $this->delIn($args);
    }

    public function delIn(array $path): MapInterface
    {
        /** @var Map $c */
        $c = $this->getClone();

        $c->_delIn($c->_data, $path);

        return $c;
    }

    public function has(...$args): bool
    {
        return $this->hasIn($args);
    }

    public function hasIn(array $path): bool
    {
        return $this->_hasIn($this->_data, $path);
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
                // do not recurse into, if array or object is empty
                if ($this->isNonScalarEmpty($v)) {
                    $callback($path, $v);
                } else {
                    $this->_walk($v, $callback);
                }
            } else {
                $callback($path, $v);
            }
            array_pop($path);
        }
    }

    protected function isNonScalarEmpty($v)
    {
        if (is_array($v)) {
            return count($v) === 0;
        }

        if (is_object($v)) {
            return count(get_object_vars($v)) === 0;
        }

        return false;
    }
}
