<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

use InvalidArgumentException;

final class TraitType extends Type
{
    public static function fromFullyQualifiedTraitName(string $classString): self
    {
        if (!trait_exists($classString)) {
            throw new InvalidArgumentException(sprintf('Type "%s" does not exist or is not a trait', $classString));
        }
        return static::getInstance($classString, Kind::TRAIT());
    }
}
