<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\Type;

use RuntimeException;

final class InterfaceType extends Type
{
    public static function fromString(string $fqin): self
    {
        if (!interface_exists($fqin)) {
            throw new RuntimeException(sprintf('Type "%s" does not exist or is not an interface', $fqin));
        }
        return static::fromValues($fqin, TypeType::INTERFACE());
    }
}