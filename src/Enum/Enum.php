<?php


namespace SolidPhp\ValueObjects\Enum;

abstract class Enum implements EnumInterface
{
    use EnumTrait;

    public function __toString()
    {
        return sprintf('%s::%s', static::class,$this->id);
    }
}
