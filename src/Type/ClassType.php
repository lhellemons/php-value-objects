<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\Type;

use RuntimeException;

final class ClassType extends Type
{
    public static function fromString(string $fqcn): self
    {
        if (!class_exists($fqcn)) {
            throw new RuntimeException(sprintf('Type "%s" does not exist or is not a class', $fqcn));
        }
        return static::fromValues($fqcn, TypeType::CLASS());
    }
}