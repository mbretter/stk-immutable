<?php

namespace Stk\Immutable\Ops;

use Stk\Immutable\ImmutableInterface;

/**
 * Class Diff
 *
 * filter out all identical values
 */
class Diff
{
    public function __invoke(ImmutableInterface $a, ImmutableInterface $b)
    {
        // filter out all identical values and keep modified and new values
        return $b->withMutations(function (ImmutableInterface $n) use ($a) {
            $n->walk(function ($path, $newVal) use ($n, $a) {
                $oldVal = call_user_func_array([$a, 'get'], $path);
                if ($oldVal === $newVal) {
                    call_user_func_array([$n, 'del'], $path);
                }
            });
        });
    }

}
