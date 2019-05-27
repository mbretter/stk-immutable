<?php

namespace Stk\Immutable;

use Closure;

interface MapInterface
{
    public function get(...$args);

    /**
     * @param mixed ...$args
     *
     * @return MapInterface
     */
    public function set(...$args);

    public function del(...$args);

    public function has(...$args): bool;


    /**
     * @param Closure $onLeaf
     *
     * @return mixed
     */
    public function walk(Closure $onLeaf);

}
