<?php

namespace Stk\Immutable;

use Closure;

class Record implements ImmutableInterface
{
    use Immutable;

    /**
     * Record constructor.
     * @param mixed $data
     */
    public function __construct($data = [])
    {
        $this->_data = $data;
    }

    public function get(mixed ...$args): mixed
    {
        if (count($args) === 0) {
            return $this->_data;
        }
        $key = $args[0];

        return array_key_exists($key, $this->_data) ? $this->_data[$key] : null;
    }

    public function set(mixed ...$args): static
    {
        if (count($args) < 1) {
            return $this;
        }

        $clone = $this->getClone();

        if (count($args) === 1) {
            $clone->_data = $args[0];
        } else {
            $clone->_data[$args[0]] = $args[1];
        }

        return $clone;
    }

    public function del(mixed ...$args): static
    {
        $clone = $this->getClone();
        unset($clone->_data[$args[0]]);

        return $clone;
    }

    public function has(mixed ...$args): bool
    {
        if (count($args) === 0) {
            return false;
        }

        return array_key_exists($args[0], $this->_data);
    }

    public function walk(Closure $callback): void
    {
        // swap params, make walk uniform
        array_walk($this->_data, function ($value, $key) use ($callback) {
            $callback($key, $value);
        });
    }
}
