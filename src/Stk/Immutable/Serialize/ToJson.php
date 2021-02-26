<?php

namespace Stk\Immutable\Serialize;

trait ToJson
{
    private function _dataToJson(mixed $data): string
    {
        return json_encode($data);
    }
}
