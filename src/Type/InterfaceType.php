<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:32
 */

namespace SolidPhp\ValueObjects\Type;

use InvalidArgumentException;
use function interface_exists;

final class InterfaceType extends Type
{
    /**
     * @param string $classString
     * @psalm-param class-string|string $classString
     *
     * @psalm-suppress MoreSpecificReturnType
     * @return self
     */
    public static function fromFullyQualifiedInterfaceName(string $classString): self
    {
        if (!interface_exists($classString)) {
            throw new InvalidArgumentException(sprintf('Type "%s" does not exist or is not an interface', $classString));
        }
        /** @psalm-suppress LessSpecificReturnStatement */
        return self::getInstance($classString, Kind::INTERFACE());
    }
}
