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
 * ```php
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

    /** @var int[] */
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
     * This method is called the first time it's needed.
     * It should call `define` for each instance to be created.
     *
     * The default behavior is to call every public factory method in the class, with
     * the expectation that those methods will call `define`. Override this method to provide
     * different behavior, for instance loading instances parametrically from an array or file.
     *
     * If you override this method, the class must be final, because child classes won't be able
     * to add instances.
     */
    protected static function defineInstances(): void
    {
        call_all_public_nullary_factory_methods(static::class);
    }

    /**
     * @param string $id
     * @param bool $throwIfNotFound
     * @return null|$this
     */
    final public static function instance(string $id, bool $throwIfNotFound = false)
    {
        static::_ensureAllInstancesInitialized();

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
        static::_ensureAllInstancesInitialized();

        return array_values(self::$instancesById[static::class]);
    }

    final public function getId(): string
    {
        static::_ensureAllInstancesInitialized();

        return array_search($this, self::$instancesById[static::class], true);
    }

    private static function _ensureAllInstancesInitialized(): void
    {
        static $_isInitializing;
        if (!$_isInitializing && !isset(self::$allInstancesInitialized[static::class])) {
            $_isInitializing = true;
            static::defineInstances();
            $_isInitializing = false;
            self::$allInstancesInitialized[static::class] = true;
        }
    }
}

function call_all_public_nullary_factory_methods(string $enumClass): void
{
    try {
        $reflectionClass = new \ReflectionClass($enumClass);

        /** @var \ReflectionMethod[] $factoryMethods */
        $factoryMethods = array_filter(
            $reflectionClass->getMethods(),
            'SolidPhp\ValueObjects\Enum\is_public_nullary_factory_method'
        );
        foreach ($factoryMethods as $method) {
            $method->invoke(null);
        }
    } catch (\ReflectionException $e) {
        throw new \DomainException(sprintf('Unable to initialize instances of Enum class %s', $enumClass), 0, $e);
    }
}

function is_public_nullary_factory_method(\ReflectionMethod $method): bool
{
    return $method->isPublic()
        && $method->isStatic()
        && $method->getNumberOfParameters() === 0
        && ($returnType = $method->getReturnType())
        && \in_array($returnType->getName(), ['self', $method->getDeclaringClass()->getName()], true);
}
