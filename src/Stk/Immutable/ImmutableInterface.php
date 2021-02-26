<?php

namespace Stk\Immutable;

use Closure;

interface ImmutableInterface
{
    public function get(mixed ...$args): mixed;

    public function del(mixed ...$args): static;

    public function set(mixed ...$args): static;

    public function has(mixed ...$args): bool;

    /*
     * Walk through the Immutable, pass field/path and value to the callback
     */
    public function walk(Closure $callback): void;

    /*
     * turns off immutability, useful for bulk updates to avoid extraneous clones
     * only available for set/del
     */
    public function withMutations(Closure $doChanges): static;
}
