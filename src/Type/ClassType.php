<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use RuntimeException;
use function call_user_func_array;
use function class_exists;
use function count;
use function get_class;

final class ClassType extends Type
{
    /** @var null|ReflectionClass */
    private $reflectionClass;

    /** @var array */
    private $confirmedStaticMethods = [];

    /**
     * @param string $classString
     * @psalm-param class-string|string $classString
     *
     * @psalm-suppress MoreSpecificReturnType
     * @return self
     */
    public static function fromFullyQualifiedClassName(string $classString): self
    {
        if (!class_exists($classString)) {
            throw new InvalidArgumentException(sprintf('Type "%s" does not exist or is not a class', $classString));
        }
        /** @psalm-suppress LessSpecificReturnStatement */
        return self::getInstance($classString, Kind::CLASS());
    }

    /**
     * @param object $instance
     *
     * @return ClassType
     */
    public static function fromInstance($instance): self
    {
        return self::fromFullyQualifiedClassName(get_class($instance));
    }

    public static function fromCaller(int $level = 1): self
    {
        /** @psalm-var array<array{class: class-string}> $trace */
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level + 2);

        if (count($trace) < ($level + 2)) {
            throw new LogicException(sprintf('Call stack is not deep enough to get the level-%d caller', $level));
        }

        if ($callingClass = $trace[$level + 1]['class']) {
            return self::fromFullyQualifiedClassName($callingClass);
        }

        throw new RuntimeException(sprintf('Level %d caller has no class', $level));
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass = $this->reflectionClass ?: new ReflectionClass($this->getFullyQualifiedName());
    }

    /**
     * @param string $methodName
     * @param mixed  ...$arguments
     *
     * @return mixed Whatever the static method returns
     * @throws \ReflectionException Not really
     */
    public function callStaticMethod(string $methodName, ...$arguments)
    {
        if (!isset($this->confirmedStaticMethods[$methodName])) {
            if (!$this->getReflectionClass()->hasMethod($methodName)) {
                throw new BadMethodCallException(
                    sprintf('Method %s::%s not found', $this->getFullyQualifiedName(), $methodName)
                );
            }

            if (!$this->getReflectionClass()->getMethod($methodName)->isStatic()) {
                throw new BadMethodCallException(
                    sprintf('Method %s::%s cannot be called statically', $this->getFullyQualifiedName(), $methodName)
                );
            }

            $this->confirmedStaticMethods[$methodName] = true;
        }

        return call_user_func_array([$this->getFullyQualifiedName(), $methodName], $arguments);
    }

    /**
     * @param string $name
     * @param array<mixed> $arguments
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call(string $name, $arguments)
    {
        return $this->callStaticMethod($name,...$arguments);
    }
}
