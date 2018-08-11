<?php

namespace SolidPhp\Type;

use SolidPhp\ValueObjects\Value\ValueObjectInterface;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

abstract class Type implements ValueObjectInterface
{
    use ValueObjectTrait;

    /** @var string */
    protected $name;

    /** @var TypeType */
    protected $type;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return TypeType
     */
    public function getType(): TypeType
    {
        return $this->type;
    }

    final public function __toString(): string
    {
        return sprintf('%s %s', $this->type->getId(), $this->getName());
    }
}