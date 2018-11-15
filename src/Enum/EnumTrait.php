<?php

namespace SolidPhp\ValueObjects\Enum;

/**
 * Trait EnumTrait
 * @package SolidPhp\ValueObjects\Enum
 *
 * This Trait gives the using class the ability to function as an Enum.
 * After using this Trait, the class can define public static "factory" methods to define its instances.
 *
 * For example, here is a very simple implementation:
 * ```
 * class FooBarEnum implements EnumInterface
 * {
 *   use EnumTrait;
 *
 *   public static function FOO(): self
 *   {
 *     return self::define('FOO');
 *   }
 *
 *   public static function BAR(): self
 *   {
 *     return self::define('BAR');
 *   }
 * }
 *```
 *
 * Afterwards you can use the factory methods, `instance` or `instances` to get the instances. You can be sure
 * that each factory methods will always return the same instance, so you can use strict equality:
 * ```
 * if ($fooOrBar === FooBarEnum::FOO()) {
 *   echo '$fooOrBar is FOO!`
 * }
 * ```
 *
 * It's also possible to add additional initialization data to the define calls to give your instances
 * additional data. Override the `initialize` method to set instance properties based on the data:
 * ```
 * class FooBarEnum implements EnumInterface
 * {
 *   use EnumTrait;
 *
 *   private $message;
 *
 *   protected function initialize($message)
 *   {
 *     $this->message = $message;
 *   }
 *
 *   public function getMessage(): string
 *   {
 *     return $this->message;
 *   }
 *
 *   public static function FOO(): self
 *   {
 *     return self::define('FOO', 'foo mama');
 *   }
 *
 *   public static function BAR(): self
 *   {
 *     return self::define('BAR', 'bar be queue');
 *   }
 * }
 *
 * echo FooBarEnum::FOO()->getMessage() // outputs 'foo mama'
 * ```
 */
trait EnumTrait /* implements EnumInterface */
{
    private static $instancesById;

    /** @var array */
    private static $allInstancesInitialized = [];

    /**
     * Default constructor.
     * Override this method in your using class to store the id or any extra data in the instance
     *
     * @param string $id
     * @param mixed[] $constructorArguments
     */
    protected function __construct(string $id, ...$constructorArguments)
    {
    }

    final protected static function define(string $id, ...$constructorArguments)
    {
        if (!isset(self::$instancesById[static::class])) {
            self::$instancesById[static::class] = [];
        }

        if (!isset(self::$instancesById[static::class][$id])) {
            self::$instancesById[static::class][$id] = new static($id, ...$constructorArguments);
        }

        return self::$instancesById[static::class][$id];
    }

    /**
     * @param string $id
     * @param bool $throwIfNotFound
     * @return null|$this
     */
    final public static function instance(string $id, bool $throwIfNotFound = false)
    {
        static::_ensureAllInstancesInitialized(static::class);

        if (isset(self::$instancesById[static::class][$id])) {
            return self::$instancesById[static::class][$id];
        }

        if ($throwIfNotFound) {
            throw new \DomainException(sprintf('Enum class %s has no instance "%s"', static::class, $id));
        }

        return null;
    }

    /**
     * @return $this[]
     */
    final public static function instances(): array
    {
        static::_ensureAllInstancesInitialized(static::class);

        return array_values(self::$instancesById[static::class]);
    }

    final public function getId(): string
    {
        static::_ensureAllInstancesInitialized(static::class);

        foreach (self::$instancesById[static::class] as $id => $instance) {
            if ($instance === $this) {
                return $id;
            }
        }

        return null;
    }

    private static function _ensureAllInstancesInitialized(string $class): void
    {
        if (!isset(self::$allInstancesInitialized[$class])) {
            initialize_all_enum_instances($class);
            self::$allInstancesInitialized[$class] = true;
        }
    }
}

function initialize_all_enum_instances(string $enumClass): void
{
    try {
        $reflectionClass = new \ReflectionClass($enumClass);

        /** @var \ReflectionMethod[] $factoryMethods */
        $factoryMethods = array_filter(
            $reflectionClass->getMethods(),
            'SolidPhp\ValueObjects\Enum\is_enum_factory_method'
        );
        foreach ($factoryMethods as $method) {
            $method->invoke(null);
        }
    } catch (\ReflectionException $e) {
        throw new \DomainException(sprintf('Unable to initialize instances of Enum class %s', $enumClass), 0, $e);
    }
}

function is_enum_factory_method(\ReflectionMethod $method): bool
{
    return $method->isPublic()
        && $method->isStatic()
        && $method->getNumberOfParameters() === 0
        && ($returnType = $method->getReturnType())
        && \in_array($returnType->getName(), ['self', $method->getDeclaringClass()->getName()], true);
}
