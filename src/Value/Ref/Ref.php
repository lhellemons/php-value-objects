<?php

namespace SolidPhp\ValueObjects\Value\Ref;

define('__SOLIDPHP_VALUEOBJECTS_NATIVE_WEAKREF_AVAILABLE', class_exists('\WeakReference'));
define('__SOLIDPHP_VALUEOBJECTS_PECL_WEAKREF_AVAILABLE', class_exists('\WeakRef'));

/**
 * @return string
 * @psalm-return class-string<Ref>
 */
function getRefClass(): string {
    if (__SOLIDPHP_VALUEOBJECTS_NATIVE_WEAKREF_AVAILABLE) {
        return NativeWeakRef::class;
    }
    if (__SOLIDPHP_VALUEOBJECTS_PECL_WEAKREF_AVAILABLE) {
        return PeclWeakRef::class;
    }

    return StrongRef::class;
}

/**
 * @template T
 * @param object $object
 * @psalm-param T $object
 * @return Ref
 * @psalm-return Ref<T>
 */
function createRef($object): Ref {
    static $refClass = null;
    /** @psalm-var class-string<Ref> $refClass */
    $refClass = $refClass ?? getRefClass();

    return new $refClass($object);
}

/**
 * Class Ref
 *
 * Provides a way of storing a reference to an object. There are three implementations:
 * StrongRef, which stores a reference to the object directly;
 * NativeWeakRef, which uses a PHP 7.4+ WeakReference to the object; and
 * PeclWeakRef, which uses a WeakRef from the weakref PECL extension.
 *
 * Weak references ensure that the object is not kept in memory after the application code
 * stops using it. To use this functionality, use PHP 7.4 or higher, or install the weakref
 * PECL extension (see https://www.php.net/manual/en/book.weakref.php)
 *
 * @internal
 * @psalm-internal SolidPhp\ValueObjects
 *
 * @template T
 */
abstract class Ref
{
    /**
     * @param object $object
     * @psalm-param T $object
     */
    final public function __construct($object)
    {
        $this->set($object);
    }

    /**
     * @param object $object
     * @psalm-param T $object
     *
     * @return Ref
     * @psalm-return Ref<T>
     */
    public static function create($object): Ref
    {
        return createRef($object);
    }

    /**
     * @return object|null
     * @psalm-return T|null
     */
    abstract public function get();

    /**
     * @param object|null $object
     * @psalm-param T|null $object
     */
    abstract public function set($object): void;

    abstract public function has(): bool;
}
