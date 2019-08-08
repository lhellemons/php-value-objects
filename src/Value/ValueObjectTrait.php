<?php

namespace SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Type\Type;
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
    static private $_instances = [];

    static protected $_componentNames;

    protected $_components = [];

    protected $_stringValue;

    final protected static function getInstance(...$values): self
    {
        if (null === static::$_componentNames) {
            static::$_componentNames = getConstructorArgumentNames(static::class);
        }

        $key = calculateKey(static::class, ...$values);

        $ref = self::$_instances[$key] = self::$_instances[$key] ?? Ref::create();
        if ($ref->has()) {
            return $ref->get();
        }

        $instance = new static(...$values);
        $instance->_components = createComponentArray(static::$_componentNames, $values);

        $ref->set($instance);

        return $instance;
    }

    final public function components(): array
    {
        return $this->_components;
    }

    public function __toString(): string
    {
        if (null === $this->_stringValue) {
            $this->_stringValue = sprintf('%s(%s)', getClassShortName(static::class), componentsToString($this->_components));
        }

        return $this->_stringValue;
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

/**
 * @param mixed ...$values
 *
 * @return string
 * @internal
 */
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

    return sprintf('scalar<%s>:%s', \gettype($value), md5(var_export($value, true)));
}

/**
 * @param string $class
 *
 * @return array
 * @throws \ReflectionException
 * @internal
 */
function getConstructorArgumentNames(string $class)
{
    $reflectionClass = new \ReflectionClass($class);
    $constructor = $reflectionClass->getConstructor();

    return array_map(
        static function (\ReflectionParameter $parameter) {
            return $parameter->getName();
        },
        $constructor ? $constructor->getParameters() : []
    );
}

/**
 * @param array $names
 * @param array $values
 *
 * @return array|false
 * @internal
 */
function createComponentArray(array $names, array $values)
{
    return array_combine(
        array_slice(
            $names + array_keys(array_fill(0, count($values), '')),
            0,
            count($values),
            false
        ),
        $values
    );
}

/**
 * @param array $components
 *
 * @return string
 * @internal
 */
function componentsToString(array $components)
{
    return implode(
        ',',
        array_map(
            static function ($key, $value) {
                $stringValue = componentValueToString($value);

                return is_numeric($key) ? $stringValue : sprintf('%s=%s', $key, $stringValue);
            },
            array_keys($components),
            array_values($components)
        )
    );
}

/**
 * @param $value
 *
 * @return mixed|string
 * @internal
 */
function componentValueToString($value)
{
    if (is_string($value)) {
        return var_export(
            strlen($value) > 100
                ? substr($value, 0, 67) . '...' . substr($value, -30)
                : $value,
            true
        );
    }

    if (is_scalar($value) || is_resource($value)) {
        return (string)$value;
    }

    if (is_array($value)) {
        return sprintf('[(%d)]', count($value));
    }

    if (is_object($value)) {
        if (method_exists($value, '__toString')) {
            return componentValueToString((string)$value);
        }

        return sprintf('{%s}', getClassShortName(\get_class($value)));
    }

    return '?';
}

/**
 * @param string $class
 *
 * @return string
 * @internal
 */
function getClassShortName(string $class): string
{
    return substr($class, strrpos($class, '\\') + 1);
}
