<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

use RuntimeException;

final class ClassType extends Type
{
    public static function fromClassString(string $classString): self
    {
        if (!\class_exists($classString)) {
            throw new RuntimeException(sprintf('Type "%s" does not exist or is not a class', $classString));
        }
        return static::getInstance($classString, Kind::CLASS());
    }
}
