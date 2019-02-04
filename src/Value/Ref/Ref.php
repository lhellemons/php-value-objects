<?php

namespace SolidPhp\ValueObjects\Value\Ref;

define('__SOLIDPHP_VALUEOBJECTS_WEAKREF_AVAILABLE', class_exists('\WeakRef'));

if (__SOLIDPHP_VALUEOBJECTS_WEAKREF_AVAILABLE) {
    function createRef(): Ref {
        return new WeakRef();
    }
} else {
    function createRef(): Ref {
        return new StrongRef();
    }
}

/**
 * Class Ref
 *
 * Provides a way of storing a reference to an object. There are two implementations:
 * StrongRef, which stores a reference to the object directly; and
 * WeakRef, which stores a PHP WeakRef to the object.
 *
 * WeakRef can only be used if the PECL extension has been installed.
 *
 * @internal
 */
abstract class Ref
{
    public static function create(): Ref
    {
        return createRef();
    }

    /**
     * @return object|null
     */
    abstract public function get();

    /**
     * @param object|null $object
     */
    abstract public function set($object): void;

    abstract public function has(): bool;
}
