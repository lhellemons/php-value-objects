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
use ReflectionClass;

final class ClassType extends Type
{
    /** @var null|ReflectionClass */
    private $reflectionClass;

    /** @var array */
    private $confirmedStaticMethods;

    public static function fromFullyQualifiedClassName(string $classString): self
    {
        if (!\class_exists($classString)) {
            throw new InvalidArgumentException(sprintf('Type "%s" does not exist or is not a class', $classString));
        }
        return static::getInstance($classString, Kind::CLASS());
    }

    /**
     * @param object $instance
     *
     * @return ClassType
     */
    public static function fromInstance($instance): self
    {
        return self::fromFullyQualifiedClassName(\get_class($instance));
    }

    public static function fromCaller(int $level = 1): self
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level + 2);

        if (\count($trace) < ($level + 2)) {
            throw new \LogicException(sprintf('Call stack is not deep enough to get the level-%d caller', $level));
        }

        if ($callingClass = $trace[$level + 1]['class']) {
            return self::fromFullyQualifiedClassName($callingClass);
        }

        throw new \RuntimeException(sprintf('Level %d caller has no class', $level));
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass = $this->reflectionClass ?: new ReflectionClass($this->getFullyQualifiedName());
    }

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

        return \call_user_func_array([$this->getFullyQualifiedName(), $methodName], $arguments);
    }

    public function __call($name, $arguments)
    {
        return $this->callStaticMethod($name,...$arguments);
    }
}
