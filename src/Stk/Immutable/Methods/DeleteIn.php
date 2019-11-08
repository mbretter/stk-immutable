<?php

namespace Stk\Immutable\Methods;

use stdClass;

trait DeleteIn
{

    /**
     * remove element from 3d data structure, return the deleted element
     * @param $data
     * @param $path
     *
     * @return array|stdClass|null
     */
    final protected function _delIn(&$data, $path)
    {
        if (count($path) === 0) {
            if (is_object($data)) {
                $data = new stdClass();
            } elseif (is_array($data)) {
                $data = [];
            } else {
                $data = null;
            }

            return $data;
        }

        if ($data === null) {
            return $data;
        }

        $elem      = &$data;
        $container = $key = null;
        foreach ($path as $f) {
            $key       = $f;
            $container = &$elem;

            if (is_object($elem)) {
                if (!property_exists($elem, $f)) {
                    return $data;
                }

                $elem = &$elem->$f;
            } elseif (is_array($elem)) {
                if (!array_key_exists($f, $elem)) {
                    return $data;
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

        return $data;
    }

}
