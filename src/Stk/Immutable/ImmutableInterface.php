<?php

namespace Stk\Immutable;

use Closure;

interface ImmutableInterface
{
    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function get(...$args);

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function del(...$args);

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function set(...$args);

    /**
     * @param mixed ...$args
     *
     * @return bool
     */
    public function has(...$args): bool;

    /**
     * Walk through the Immutable, pass field/path and value to the callback
     *
     * @param Closure $callback
     *
     * @return mixed
     */
    public function walk(Closure $callback);

    /**
     * turns off immutability, useful for bulk updates to avoid extraneous clones
     * only available for set/del
     * @param Closure $doChanges the Map itself is passed as the only parameter
     *
     * @return static
     */
    public function withMutations(Closure $doChanges);
}
