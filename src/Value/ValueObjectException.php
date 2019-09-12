<?php

namespace SolidPhp\ValueObjects\Value;

use LogicException;
use SolidPhp\ValueObjects\Type\Type;

class ValueObjectException extends LogicException
{
    public static function cannotMutate(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be mutated', (string)$type));
    }

    public static function cannotUnserialize(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be unserialized', (string)$type));
    }

    public static function cannotClone(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be cloned', (string)$type));
    }
}
