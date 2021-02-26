<?php

namespace Stk\Immutable\Methods;

trait ReadIn
{
    final protected function _seek(mixed &$target, array $path, ?bool &$found = true): mixed
    {
        $found = true;
        $elem  = $target;
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

    final protected function _hasIn(mixed &$data, array $path): bool
    {
        if (count($path) === 0) {
            return $data !== null;
        }

        $this->_seek($data, $path, $found);

        return $found;
    }

    final protected function _getIn(mixed &$data, array $path): mixed
    {
        if (count($path) === 0) {
            return $data;
        }

        $elem = $this->_seek($data, $path, $found);

        if (!$found) {
            return null;
        }

        return $elem;
    }


}
