<?php

namespace Stk\Immutable\Additions;

use stdClass;

trait ToArray
{
    protected function dataToArray($data)
    {
        if (is_object($data) && $data instanceof stdClass) {
            $ovs = get_object_vars($data);

            // keep empty stdClasses
            if (!count($ovs))
                return $data;

            $data = $ovs;
        }

        if (is_array($data)) {
            return array_map([$this, 'dataToArray'], $data);
        } else {
            return $data;
        }
    }
}
