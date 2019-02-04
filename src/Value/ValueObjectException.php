<?php

namespace SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Type\Type;

class ValueObjectException extends \LogicException
{
    public static function cannotMutate(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be mutated', $type));
    }

    public static function cannotUnserialize(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be unserialized', $type));
    }

    public static function cannotClone(Type $type): self
    {
        return new self(sprintf('%s is a value object type, it cannot be cloned', $type));
    }
}
