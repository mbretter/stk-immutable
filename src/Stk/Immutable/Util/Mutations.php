<?php

namespace Stk\Immutable\Util;

use Stk\Immutable\ImmutableInterface;

/**
 * generic placeholder for calculated diffs between immutables
 */
class Mutations
{
    /** @var ImmutableInterface */
    protected $_modified;

    /** @var ImmutableInterface */
    protected $_deleted;

    /** @var ImmutableInterface */
    protected $_old;

    /** @var ImmutableInterface */
    protected $_new;

    public function __construct(ImmutableInterface $old, ImmutableInterface $new)
    {
        $this->_old = $old;
        $this->_new = $new;
        $this->build();
    }

    /**
     * @return ImmutableInterface
     */
    public function getModified()
    {
        return $this->_modified;
    }

    /**
     * @return ImmutableInterface
     */
    public function getDeleted()
    {
        return $this->_deleted;
    }

    protected function build()
    {
        $this->_modified = clone $this->_new;

        // filter out all identical values and keep modified
        $this->_new->walk(function ($path, $newVal) {

            $oldVal = call_user_func_array([$this->_old, 'get'], $path);
            if ($oldVal === $newVal) {
                $this->_modified->withMutations(function (ImmutableInterface $m) use ($path) {
                    call_user_func_array([$m, 'del'], $path);
                });
            }
        });

        $this->_deleted = clone $this->_old;

        // filter out all existing values (intersect)
        $this->_old->walk(function ($path, $oldVal) {
            if (call_user_func_array([$this->_new, 'has'], $path)) {
                $this->_deleted->withMutations(function (ImmutableInterface $m) use ($path) {
                    call_user_func_array([$m, 'del'], $path);
                });
            }
        });
    }

}
