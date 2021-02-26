<?php

namespace Stk\Immutable;

use Closure;

trait Immutable
{
    protected mixed $_data = null;
    private bool $_isMutable = false;

    public function __clone()
    {
        if (is_object($this->_data)) {
            $this->_data = clone $this->_data;
        }
    }

    public function withMutations(Closure $doChanges): static
    {
        $c             = $this->getClone();
        $c->_isMutable = true;
        $doChanges($c);
        $c->_isMutable = false;

        return $c;
    }

    protected function getClone(): static
    {
        return $this->_isMutable ? $this : clone($this);
    }

}
