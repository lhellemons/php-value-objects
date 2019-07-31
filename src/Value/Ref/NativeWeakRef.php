<?php

namespace SolidPhp\ValueObjects\Value\Ref;

use \WeakReference;

/**
 * Class NativeWeakRef
 * A Ref implementation that uses PHP 7.4+ native WeakReference
 * See https://wiki.php.net/rfc/weakrefs
 *
 * @internal
 */
class NativeWeakRef extends Ref
{
    /** @var WeakReference|null */
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
        $this->weakRef = WeakReference::create($object);
    }

    public function has(): bool
    {
        return (bool)$this->get();
    }
}
