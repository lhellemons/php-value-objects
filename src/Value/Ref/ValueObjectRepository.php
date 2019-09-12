<?php

namespace SolidPhp\ValueObjects\Value\Ref;

use Closure;
use RuntimeException;
use stdClass;
use function count;
use function gettype;
use function is_array;
use function is_object;
use function is_resource;

/**
 * ValueObjectRepository
 *
 * This is a package-internal class that is responsible for instantiating and keeping
 * references to value objects. It is used by the value object
 *
 * @internal
 *
 * @template T
 */
final class ValueObjectRepository
{
    /** @psalm-var array<class-string<T>, self<T>> */
    static private $repositories = [];

    /** @var Ref[] */
    private $instances = [];

    /**
     * @psalm-var Closure(array<mixed>):T
     */
    private $instanceFactory;

    /**
     * @param class-string<T> $class
     */
    private function __construct($class)
    {
        $this->instanceFactory = self::createFactory($class);
    }

    /**
     * @param string $class
     * @psalm-param class-string<T> $class
     *
     * @return self
     * @psalm-return self<T>
     */
    public static function getRepository($class): self
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('type "%s" does not exist or is not a class', $class));
        }

        /** @psalm-var self<T> */
        return self::$repositories[$class] = self::$repositories[$class] ?? new self($class);
    }

    /**
     * @param string $class
     * @psalm-param class-string<T> $class
     * @param mixed ...$values
     *
     * @return object
     * @psalm-return T
     */
    public static function getInstanceOfClass($class, ...$values)
    {
        /** @psalm-var T */
        return self::getRepository($class)->getInstance(...$values);
    }

    /**
     * @param mixed ...$values
     *
     * @return object
     * @psalm-return T
     */
    public function getInstance(...$values)
    {
        $key = self::calculateKey(...$values);

        /** @psalm-var T $instance */
        $instance = (isset($this->instances[$key]) && $this->instances[$key]->has()) ? $this->instances[$key]->get() : ($this->instanceFactory)($values);

        $ref = $this->instances[$key] = $this->instances[$key] ?? Ref::create($instance);
        if (!$ref->has()) {
            $ref->set($instance);
        }

        return $instance;
    }

    /**
     * @param string $class
     * @psalm-param class-string<T> $class
     *
     * @return Closure
     * @psalm-return Closure(array<mixed>):T
     */
    private static function createFactory($class): Closure
    {
        /** @psalm-var Closure(array<mixed>):T */
        return Closure::fromCallable(
            static function (array $values) {
                /**
                 * @psalm-suppress TooManyArguments
                 * @psalm-suppress MixedArgument
                 */
                return new self(...array_values($values));
            }
            )->bindTo(null, $class);
    }

    /**
     * @param mixed ...$values
     *
     * @return string
     */
    private static function calculateKey(...$values): string
    {
        if (count($values) === 0) {
            return '()';
        }

        if (count($values) > 1) {
            return implode('|', array_map([__CLASS__, 'calculateKey'], $values));
        }

        [$value] = $values;

        if ($value instanceof stdClass || is_array($value)) {
            return 'array:' . http_build_query(array_map([__CLASS__, 'calculateKey'], (array)$value));
        }

        if (is_object($value)) {
            return 'object:' . spl_object_hash($value);
        }

        if (null === $value) {
            return 'null';
        }

        if (is_resource($value)) {
            // note: this works because PHP stringifies resources as 'resource id #x' and never reuses x during a script run
            return 'resource:' . (string)$value;
        }

        return sprintf('scalar<%s>:%s', gettype($value), (string)$value);
    }
}
