<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

final class InterfaceType extends Type
{
    public static function fromFullyQualifiedInterfaceName(string $classString): self
    {
        if (!\interface_exists($classString)) {
            throw new \InvalidArgumentException(sprintf('Type "%s" does not exist or is not an interface', $classString));
        }
        return static::getInstance($classString, Kind::INTERFACE());
    }
}
