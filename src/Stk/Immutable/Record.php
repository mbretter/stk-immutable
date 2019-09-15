<?php

namespace Stk\Immutable;

use Closure;

class Record implements ImmutableInterface
{
    use Immutable;

    public function __construct($data = [])
    {
        $this->_data = $data;
    }

    /**
     * @param array $args
     *
     * @return mixed|null
     */
    public function get(...$args)
    {
        if (count($args) === 0) {
            return $this->_data;
        }
        $key = $args[0];

        return array_key_exists($key, $this->_data) ? $this->_data[$key] : null;
    }

    /**
     * @param mixed ...$args
     *
     * @return ImmutableInterface
     */
    public function set(...$args): ImmutableInterface
    {
        if (count($args) < 2) {
            return $this;
        }

        /** @var ImmutableInterface $clone */
        $clone = $this->getClone();

        $clone->_data[$args[0]] = $args[1];

        return $clone;
    }

    /**
     * @param mixed ...$args
     *
     * @return MapInterface
     */
    public function del(...$args): ImmutableInterface
    {
        /** @var ImmutableInterface $clone */
        $clone = $this->getClone();
        unset($clone->_data[$args[0]]);

        return $clone;
    }

    /**
     * @param mixed ...$args
     *
     * @return bool
     */
    public function has(...$args): bool
    {
        if (count($args) === 0) {
            return false;
        }

        return array_key_exists($args[0], $this->_data);
    }

    /**
     * @param Closure $callback
     */
    public function walk(Closure $callback)
    {
        // swap params, make walk uniform
        array_walk($this->_data, function ($value, $key) use ($callback) {
            $callback($key, $value);
        });
    }
}
