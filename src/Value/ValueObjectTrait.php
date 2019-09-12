<?php

namespace SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Value\Ref\ValueObjectRepository;

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
 *
 * @template T
 */
trait ValueObjectTrait
{
    /**
     * @param mixed ...$values
     * @psalm-param T ...$values
     *
     * @psalm-suppress MixedInferredReturnType
     * @return static&object
     * @psalm-return static<T>
     */
    final protected static function getInstance(...$values): self
    {
        return ValueObjectRepository::getInstanceOfClass(static::class, ...$values);
    }

    /**
     * @param string $name
     */
    public function __get($name)
    {
        throw new ValueObjectException(
            sprintf('%s is a value object class, its properties cannot be gotten directly', static::class)
        );
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        throw new ValueObjectException(
            sprintf('%s is a value object class, its properties cannot be inspected.', static::class)
        );
    }


    /**
     * @param string $name
     * @param mixed $value
     */
    final public function __set($name, $value)
    {
        throw ValueObjectException::cannotMutate(ClassType::of(static::class));
    }

    final public function __wakeup()
    {
        throw ValueObjectException::cannotUnserialize(ClassType::of(static::class));
    }

    /**
     * @param array $an_array
     */
    final public static function __set_state($an_array)
    {
        throw ValueObjectException::cannotUnserialize(ClassType::of(static::class));
    }

    final public function __clone()
    {
        throw ValueObjectException::cannotClone(ClassType::of(static::class));
    }
}
