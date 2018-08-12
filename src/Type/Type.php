<?php

namespace SolidPhp\ValueObjects\Type;

use SolidPhp\ValueObjects\Value\ValueObjectInterface;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

abstract class Type implements ValueObjectInterface
{
    use ValueObjectTrait;

    /** @var string */
    protected $name;

    /** @var Kind */
    protected $kind;

    public function getName(): string
    {
        return $this->name;
    }

    public function getKind(): Kind
    {
        return $this->kind;
    }

    final public function __toString(): string
    {
        return sprintf('%s %s', $this->kind->getId(), $this->getName());
    }

    final public function isSuperTypeOf(Type $type): bool
    {
        return is_subclass_of($type->getName(), $this->getName(),true);
    }

    final public function isSubTypeOf(Type $type): bool
    {
        return $type->isSuperTypeOf($this);
    }

    final public function envelops(Type $type): bool
    {
        return $this === $type || $this->isSuperTypeOf($type);
    }

    final public function fits(Type $type): bool
    {
        return $type->envelops($this);
    }
}