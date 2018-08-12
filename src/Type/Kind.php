<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:31
 */

namespace SolidPhp\ValueObjects\Type;

use SolidPhp\ValueObjects\Enum\EnumInterface;
use SolidPhp\ValueObjects\Enum\EnumTrait;

final class Kind implements EnumInterface
{
    use EnumTrait;

    public static function CLASS(): self
    {
        return self::define('CLASS');
    }

    public static function INTERFACE(): self
    {
        return self::define('INTERFACE');
    }

    public static function TRAIT(): self
    {
        return self::define('TRAIT');
    }
}