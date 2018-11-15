<?php

namespace SolidPhp\ValueObjects\Type;

use SolidPhp\ValueObjects\Value\ValueObjectTrait;

abstract class Type
{
    use ValueObjectTrait;

    /** @var string */
    protected $name;

    /** @var Kind */
    protected $kind;

    private function __construct(string $name, Kind $kind)
    {
        $this->name = $name;
        $this->kind = $kind;
    }

    public static function named($fullyQualifiedName): self
    {
        if (class_exists($fullyQualifiedName)) {
            return ClassType::fromFullyQualifiedClassName($fullyQualifiedName);
        }
        if (interface_exists($fullyQualifiedName)) {
            return InterfaceType::fromFullyQualifiedInterfaceName($fullyQualifiedName);
        }
        if (trait_exists($fullyQualifiedName)) {
            return TraitType::fromFullyQualifiedTraitName($fullyQualifiedName);
        }

        throw new \InvalidArgumentException(sprintf('Unsupported type: %s', $fullyQualifiedName));
    }

    public static function of(object $instance): ClassType
    {
        return ClassType::fromInstance($instance);
    }

    public function getFullyQualifiedName(): string
    {
        return $this->name;
    }

    public function getKind(): Kind
    {
        return $this->kind;
    }

    final public function __toString(): string
    {
        return sprintf('%s %s', $this->kind->getId(), $this->getFullyQualifiedName());
    }

    final public function isSuperTypeOf(Type $type): bool
    {
        return $type === $this || is_subclass_of($type->getFullyQualifiedName(), $this->getFullyQualifiedName(),true);
    }

    final public function isSubTypeOf(Type $type): bool
    {
        return $type->isSuperTypeOf($this);
    }

    final public function isInstance(object $object): bool
    {
        return $this->isSuperTypeOf(ClassType::fromInstance($object));
    }
}
