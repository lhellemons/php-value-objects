<?php

namespace SolidPhp\ValueObjects\Type;

use InvalidArgumentException;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

abstract class Type
{
    use ValueObjectTrait;

    /**
     * @var string
     * @psalm-var class-string
     */
    protected $name;

    /** @var string|null */
    protected $shortName;

    /** @var Kind */
    protected $kind;

    /**
     * @param string $name
     * @psalm-param class-string $name
     * @param Kind   $kind
     */
    protected function __construct(string $name, Kind $kind)
    {
        $this->name = $name;
        $this->kind = $kind;
    }

    /**
     * @param string|object $source
     *
     * @return Type
     */
    public static function of($source): self
    {
        if (is_object($source)) {
            return ClassType::fromInstance($source);
        }
        if (class_exists($source)) {
            return ClassType::fromFullyQualifiedClassName($source);
        }
        if (interface_exists($source)) {
            return InterfaceType::fromFullyQualifiedInterfaceName($source);
        }
        if (trait_exists($source)) {
            return TraitType::fromFullyQualifiedTraitName($source);
        }

        throw new InvalidArgumentException(sprintf('Unsupported type: %s', $source));
    }

    /**
     * @param object $instance
     *
     * @return ClassType
     */
    public static function ofInstance($instance): ClassType
    {
        return ClassType::fromInstance($instance);
    }

    /**
     * @return string
     * @psalm-return class-string
     */
    public function getFullyQualifiedName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName = $this->shortName ?: substr($this->name, (int)strrpos($this->name, '\\') + 1);
    }

    public function getKind(): Kind
    {
        return $this->kind;
    }

    final public function __toString(): string
    {
        return sprintf('%s %s', $this->kind->getId(), $this->getFullyQualifiedName());
    }

    final public function isSupertypeOf(Type $type): bool
    {
        return $type === $this || is_subclass_of($type->getFullyQualifiedName(), $this->getFullyQualifiedName(),true);
    }

    final public function isSubtypeOf(Type $type): bool
    {
        return $type->isSupertypeOf($this);
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    final public function isInstance($object): bool
    {
        return $this->isSupertypeOf(ClassType::fromInstance($object));
    }
}
