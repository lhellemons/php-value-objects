<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\Type;

use RuntimeException;

final class TraitType extends Type
{
    public static function fromString(string $fqtn): self
    {
        if (!trait_exists($fqtn)) {
            throw new RuntimeException(sprintf('Type "%s" does not exist or is not a trait', $fqtn));
        }
        return static::fromValues($fqtn, TypeType::TRAIT());
    }
}