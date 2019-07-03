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

    /**
     * build the modified and deleted data objects
     */
    protected function build()
    {
        // filter out all identical values and keep modified
        $this->_modified = $this->_new->withMutations(function (ImmutableInterface $n) {
            $n->walk(function ($path, $newVal) use ($n) {
                $oldVal = call_user_func_array([$this->_old, 'get'], $path);
                if ($oldVal === $newVal) {
                    call_user_func_array([$n, 'del'], $path);
                }
            });
        });

        // filter out all values which are still present in the new object
        $this->_deleted = $this->_old->withMutations(function (ImmutableInterface $o) {
            $o->walk(function ($path, $oldVal) use ($o) {
                if (call_user_func_array([$this->_new, 'has'], $path)) {
                    call_user_func_array([$o, 'del'], $path);
                }
            });
        });

    }

}
