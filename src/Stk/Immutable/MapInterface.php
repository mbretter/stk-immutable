<?php

namespace Stk\Immutable;

interface MapInterface extends ImmutableInterface
{
    public function getIn(array $path): mixed;

    public function setIn(array $path, mixed $value): static;

    public function delIn(array $path): static;

    public function hasIn(array $path): bool;
}
