<?php

namespace Stk\Immutable;

class Bag
{
    use Immutable;

    public function __construct($data = null)
    {
        $this->_data = $data;
    }
}
