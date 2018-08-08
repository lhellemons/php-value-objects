<?php


namespace SolidPhp\ValueObjects\Value;

interface ValueObjectInterface
{
    public function equals(ValueObjectInterface $object): bool;
}
