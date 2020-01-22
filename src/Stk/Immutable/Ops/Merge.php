<?php

namespace Stk\Immutable\Ops;

use Stk\Immutable\ImmutableInterface;

/**
 * Class Merge
 *
 * merge b into a
 */
class Merge
{
    public function __invoke(ImmutableInterface $a, ImmutableInterface $b)
    {
        return $a->withMutations(function (ImmutableInterface $n) use ($b) {
            $b->walk(function ($path, $val) use ($n) {
                array_push($path, $val);
                call_user_func_array([$n, 'set'], $path);
            });
        });
    }

}
