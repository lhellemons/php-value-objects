<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

use RuntimeException;

final class TraitType extends Type
{
    public static function fromClassString(string $classString): self
    {
        if (!trait_exists($classString)) {
            throw new RuntimeException(sprintf('Type "%s" does not exist or is not a trait', $classString));
        }
        return static::fromValues($classString, Kind::TRAIT());
    }
}