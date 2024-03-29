<?php

namespace Stk\Immutable\Serialize;

use stdClass;

trait ToArray
{
    private function _dataToArray(mixed $data): mixed
    {
        if (is_object($data) && $data instanceof stdClass) {
            $ovs = get_object_vars($data);

            // keep empty stdClasses
            if (!count($ovs)) {
                return $data;
            }

            $data = $ovs;
        }

        if (is_array($data)) {
            return array_map([$this, '_dataToArray'], $data);
        } else {
            return $data;
        }
    }
}
