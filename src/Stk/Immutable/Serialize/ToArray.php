<?php

namespace Stk\Immutable\Serialize;

use stdClass;

trait ToArray
{
    /**
     * @param $data
     *
     * @return array|stdClass
     */
    private function _dataToArray($data)
    {
        if (is_object($data) && $data instanceof stdClass) {
            $ovs = get_object_vars($data);

            // keep empty stdClasses
            if (!count($ovs))
                return $data;

            $data = $ovs;
        }

        if (is_array($data)) {
            return array_map([$this, '_dataToArray'], $data);
        } else {
            return $data;
        }
    }
}
