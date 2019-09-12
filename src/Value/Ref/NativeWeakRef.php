<?php

namespace SolidPhp\ValueObjects\Value\Ref;

/**
 * Class NativeWeakRef
 * A Ref implementation that uses PHP 7.4+ native WeakReference
 * See https://wiki.php.net/rfc/weakrefs
 *
 * @internal
 * @psalm-suppress UndefinedDocblockClass
 */
class NativeWeakRef extends Ref
{
    /** @var \WeakReference|null */
    private $weakRef;

    public function get()
    {
        /** @psalm-suppress UndefinedDocblockClass */
        return $this->weakRef ? $this->weakRef->get() : null;
    }

    public function set($object): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @psalm-suppress MixedAssignment
         */
        $this->weakRef = \WeakReference::create($object);
    }

    public function has(): bool
    {
        return (bool)$this->get();
    }
}
