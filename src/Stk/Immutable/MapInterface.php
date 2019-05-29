<?php

namespace Stk\Immutable;

interface MapInterface extends ImmutableInterface
{
    /**
     * @param array $path
     *
     * @return mixed
     */
    public function getIn(array $path);

    /**
     * @param array $path
     * @param mixed $value
     *
     * @return MapInterface
     */
    public function setIn(array $path, $value): MapInterface;

    /**
     * @param array $path
     *
     * @return MapInterface
     */
    public function delIn(array $path): MapInterface;

    /**
     * @param array $path
     *
     * @return bool
     */
    public function hasIn(array $path): bool;


}
