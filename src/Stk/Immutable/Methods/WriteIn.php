<?php

namespace Stk\Immutable\Methods;

use stdClass;

trait WriteIn
{
    final protected function _setIn(&$data, array $path, $val)
    {
        // set($data)
        if (!count($path)) {
            $data = $val;

            return $data;
        }

        if ($data === null) {
            $data = new stdClass();
        }

        $elem = &$data;

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

        return $data;
    }


}
