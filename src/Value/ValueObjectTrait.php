<?php

namespace SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Value\Ref\Ref;

/**
 * Trait ValueObjectTrait
 *
 * This trait gives the using class the ability to function as a value object
 * that can be compared by-reference.
 *
 * Classes using this Trait should do the following:
 * - Define a private (or protected, if subclasses should be supported) constructor method. This method
 *   can accept any parameters, just like a regular constructor.
 * - Define one or more static factory methods. These factory methods can call self::getInstance to obtain
 *   the correct instance.
 *   <b>Important</b>: The call to getInstance must provide the arguments defined in the
 *   constructor, in the same order. Treat it as if you were calling the constructor directly.
 */
trait ValueObjectTrait
{
    /** @var Ref[] */
    static private $instances = [];

    protected function __construct(...$arguments)
    {
        throw new ValueObjectException(
            sprintf(
                'Class %s uses ValueObjectTrait but does not define a private or protected constructor.',
                static::class
            )
        );
    }

    final protected static function getInstance(...$values): self
    {
        $key = calculateKey(static::class, ...$values);

        $ref = self::$instances[$key] = self::$instances[$key] ?? Ref::create();
        if ($ref->has()) {
            return $ref->get();
        }

        $instance = new static(...$values);
        $ref->set($instance);

        return $instance;
    }

    public function __get($name)
    {
        throw new ValueObjectException(
            sprintf('%s is a value object class, its properties cannot be gotten directly', static::class)
        );
    }

    public function __isset($name)
    {
        throw new ValueObjectException(
            sprintf('%s is a value object class, its properties cannot be inspected.', static::class)
        );
    }

    final public function __set($name, $value)
    {
        throw ValueObjectException::cannotMutate(ClassType::of(static::class));
    }

    final public function __wakeup()
    {
        throw ValueObjectException::cannotUnserialize(ClassType::of(static::class));
    }

    final public static function __set_state($an_array)
    {
        throw ValueObjectException::cannotUnserialize(ClassType::of(static::class));
    }

    final public function __clone()
    {
        throw ValueObjectException::cannotClone(ClassType::of(static::class));
    }
}

function calculateKey(...$values): string
{
    if (\count($values) === 0) {
        return '()';
    }

    if (\count($values) > 1) {
        return implode('|', array_map('SolidPhp\ValueObjects\Value\calculateKey', $values));
    }

    [$value] = $values;

    if ($value instanceof \stdClass || \is_array($value)) {
        return 'array:' . http_build_query(array_map('SolidPhp\ValueObjects\Value\calculateKey', (array)$value));
    }

    if (\is_object($value)) {
        return 'object:' . spl_object_hash($value);
    }

    if (null === $value) {
        return 'null';
    }

    if (\is_resource($value)) {
        // note: this works because PHP stringifies resources as 'resource id #x' and never reuses x during a script run
        return 'resource:' . $value;
    }

    return sprintf('scalar<%s>:%s', \gettype($value), $value);
}
