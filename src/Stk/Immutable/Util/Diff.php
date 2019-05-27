<?php

namespace Stk\Immutable\Util;

use Stk\Immutable\MapInterface;

class Diff
{
    public function modified(MapInterface $old, MapInterface $new)
    {
        $modified = clone $new;

        $new->walk(function ($path, $newVal) use ($old, &$modified) {

            $oldVal = $old->get($path);
            if ($oldVal === $newVal) {
                $modified = $modified->del($path);
            }
        });

        return $modified;
    }

    public function deleted(MapInterface $old, MapInterface $new)
    {
        $deleted = clone $old;

        $old->walk(function ($path, $oldVal) use ($new, &$deleted) {
            if ($new->has($path)) {
                $deleted = $deleted->del($path);
            }
        });

        return $deleted;
    }
}
