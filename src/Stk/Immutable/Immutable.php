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
        $c             = $this->getClone();
        $c->_isMutable = true;
        $cb($c);
        $c->_isMutable = false;

        return $c;
    }

    protected function getClone()
    {
        return $this->_isMutable ? $this : clone($this);
    }

}
