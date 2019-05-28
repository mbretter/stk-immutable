<?php

namespace Stk\Immutable;

use stdClass;

trait Immutable
{
    protected $_data = null;

    public function __clone()
    {
        if (is_object($this->_data)) {
            $this->_data = clone $this->_data;
        }
    }

    protected function seek($path, &$found = true)
    {
        $found = true;
        $elem  = $this->_data;
        foreach ($path as $f) {
            if (is_object($elem)) {
                if (!property_exists($elem, $f)) {
                    $found = false;

                    return null;
                }

                $elem = $elem->$f;
            } elseif (is_array($elem)) {
                if (!array_key_exists($f, $elem)) {
                    $found = false;

                    return null;
                }

                $elem = $elem[$f];
            } else {
                $found = false;

                return null;
            }
        }

        return $elem;

    }

    public function has(...$args): bool
    {
        $path = $this->_makePathFromArgs($args);

        if (count($path) === 0) {
            return $this->_data === null;
        }

        $this->seek($path, $found);

        return $found;
    }

    public function get(...$args)
    {
        $path = $this->_makePathFromArgs($args);

        if (count($path) === 0) {
            return is_object($this->_data) ? clone($this->_data) : $this->_data;
        }

        $elem = $this->seek($path, $found);

        if (!$found) {
            return null;
        }

        if (is_object($elem)) {
            return clone($elem);
        } else {
            return $elem;
        }
    }

    public function set(...$args)
    {
        $numArgs = count($args);
        if ($numArgs < 1) {
            return $this;
        }

        $val = array_pop($args);

        $c = clone($this);

        // set($data)
        if ($numArgs == 1) {
            $c->_data = $val;

            return $c;
        }

        $path = $this->_makePathFromArgs($args);

        if ($c->_data === null) {
            $c->_data = new stdClass();
        }

        $elem = &$c->_data;

        $field = null;
        $depth = count($path) - 1;
        foreach ($path as $idx => $f) {
            if (is_object($elem)) {
                // dont make new elements on the last leaf
                if ($idx != $depth && !property_exists($elem, $f)) {
                    $elem->$f = new stdClass();
                }

                if ($idx < $depth) {
                    $elem = &$elem->$f;
                }
            } elseif (is_array($elem)) {
                if ($idx != $depth && !array_key_exists($f, $elem)) {
                    $elem[$f] = [];
                }

                if ($idx < $depth) {
                    $elem = &$elem[$f];
                }
            }

            $field = $f;
        }

        assert($field !== null);

        if (is_object($elem)) {
            // copy on write scheme
            $elem         = clone($elem);
            $elem->$field = $val;
        } else {
            $elem[$field] = $val;
        }

        return $c;
    }

    public function del(...$args)
    {
        $path = $this->_makePathFromArgs($args);

        $c = clone($this);

        if (count($path) === 0) {
            $c->_data = null;

            return $c;
        }

        if ($c->_data === null) {
            return $c;
        }

        $elem      = &$c->_data;
        $container = $key = null;
        foreach ($path as $f) {
            $key       = $f;
            $container = &$elem;

            if (is_object($elem)) {
                if (!property_exists($elem, $f)) {
                    return $c;
                }

                $elem = &$elem->$f;
            } elseif (is_array($elem)) {
                if (!array_key_exists($f, $elem)) {
                    return $c;
                }

                $elem = &$elem[$f];
            }
        }

        if (is_array($container)) {
            unset($container[$key]);
        } elseif (is_object($container)) {
            // copy on write scheme
            $container = clone $container;
            unset($container->$key);
        }

        return $c;
    }

    private function _makePathFromArgs($args)
    {
        $numArgs = count($args);
        if ($numArgs == 1 && is_string($args[0])) {
            return [$args[0]];
        } // get([$f1, $f2])
        elseif ($numArgs == 1 && is_array($args[0])) {
            return $args[0];
        } // get($f1, $f2)
        elseif ($numArgs > 1) {
            return $args;
        } else {
            return [];
        }
    }
}
