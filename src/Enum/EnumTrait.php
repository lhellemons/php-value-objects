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
    /** @var array EnumTrait[] */
    private static $instances = [];

    /** @var bool */
    private static $allInstancesInitialized = false;

    /** @var string */
    private $id;

    /**
     * Constructs a new instance of this Enum class.
     * This method is made private to enforce the use of factory methods
     * @param string $id
     * @param array $initializationData
     */
    private function __construct(string $id, ...$initializationData)
    {
        $this->id = $id;

        $this->initialize(...$initializationData);
    }

    /**
     * Override this function to initialize your instance with custom data
     * @param mixed ...$data
     */
    protected function initialize(...$data): void
    {
    }

    final protected static function define(string $id, ...$initializationData)
    {
        if (!isset(static::$instances[$id])) {
            static::$instances[$id] = new static($id, ...$initializationData);
        }

        return static::$instances[$id];
    }

    /**
     * @param string $id
     * @param bool $thowIfNotFound
     * @return null|$this
     */
    final public static function instance(string $id, bool $thowIfNotFound = false)
    {
        static::_ensureAllInstancesInitialized();

        if (isset(static::$instances[$id])) {
            return static::$instances[$id];
        }

        if ($thowIfNotFound) {
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

        return array_values(static::$instances);
    }

    final public function getId(): string
    {
        return $this->id;
    }

    private static function _ensureAllInstancesInitialized(): void
    {
        if (!static::$allInstancesInitialized) {
            initialize_all_enum_instances(static::class);
            static::$allInstancesInitialized = true;
        }
    }
}

function initialize_all_enum_instances(string $enumClass): void
{
    try {
        $reflectionClass = new \ReflectionClass($enumClass);

        /** @var \ReflectionMethod[] $factoryMethods */
        $factoryMethods = array_filter($reflectionClass->getMethods(), 'SolidPhp\ValueObjects\Enum\is_enum_factory_method');
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
        && \in_array($returnType->getName(),['self', $method->getDeclaringClass()->getName()], true);
}
