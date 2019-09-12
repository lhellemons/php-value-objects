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
    /**
     * @param string $classString
     * @psalm-param class-string|string $classString
     *
     * @psalm-suppress MoreSpecificReturnType
     * @return self
     */
    public static function fromFullyQualifiedTraitName(string $classString): self
    {
        if (!trait_exists($classString)) {
            throw new InvalidArgumentException(sprintf('Type "%s" does not exist or is not a trait', $classString));
        }
        /** @psalm-suppress LessSpecificReturnStatement */
        return static::getInstance($classString, Kind::TRAIT());
    }
}
