<?php

namespace SolidPhp\ValueObjects\Value\Ref;

use \WeakRef as PhpWeakRef;

/**
 * Class WeakRef
 * @internal
 */
class WeakRef extends Ref
{
    /** @var PhpWeakRef|null */
    private $weakRef;

    /**
     * @return object|null
     */
    public function get()
    {
        return $this->weakRef ? $this->weakRef->get() : null;
    }

    /**
     * @param object|null $object
     */
    public function set($object): void
    {
        $this->weakRef = new PhpWeakRef($object);
    }

    public function has(): bool
    {
        return $this->weakRef && $this->weakRef->valid();
    }
}
