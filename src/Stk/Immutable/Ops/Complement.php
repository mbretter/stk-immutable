<?php

namespace Stk\Immutable\Ops;

use Stk\Immutable\ImmutableInterface;

/**
 * Class Complement
 *
 * build a without b
 *
 */
class Complement
{
    public function __invoke(ImmutableInterface $a, ImmutableInterface $b): ImmutableInterface
    {
        // filter out all values which are still present in the new object
        return $a->withMutations(function (ImmutableInterface $a) use ($b) {
            $a->walk(function ($path, $oldVal) use ($a, $b) {
                if (call_user_func_array([$b, 'has'], $path)) {
                    call_user_func_array([$a, 'del'], $path);
                }
            });
        });
    }

}
