<?php

namespace Stk\Immutable;

use Closure;

trait Immutable
{
    protected $_data = null;
    private $_isMutable = false;

    public function __clone()
    {
        if (is_object($this->_data)) {
            $this->_data = clone $this->_data;
        }
    }

    public function withMutations(Closure $cb)
    {
        $this->_isMutable = true;
        $cb($this);
        $this->_isMutable = false;

        return $this;
    }

    protected function getClone()
    {
        return $this->_isMutable ? $this : clone($this);
    }

}
