<?php

namespace Stk\Immutable\Serialize;

trait ToJson
{
    /**
     * @param $data
     *
     * @return string
     */
    private function _dataToJson($data)
    {
        return json_encode($data);
    }
}
