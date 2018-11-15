<?php

namespace Test\SolidPhp\ValueObjects\Type;

use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Type\InterfaceType;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingClass;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingInterface;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingTrait;

class InterfaceTypeTest extends TestCase
{
    /**
     * @dataProvider getCasesForFromFullyQualifiedInterfaceName
     * @param string $fullyQualifiedInterfaceName
     * @param bool $expectedResult
     */
    public function testFromFullyQualifiedInterfaceName(string $fullyQualifiedInterfaceName, bool $expectedResult): void
    {
        if ($expectedResult) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(\InvalidArgumentException::class);
        }

        InterfaceType::fromFullyQualifiedInterfaceName($fullyQualifiedInterfaceName);
    }

    public function getCasesForFromFullyQualifiedInterfaceName(): array
    {
        return [
            'existing interface' => [ExistingInterface::class, true],
            'non-existing interface' => ['NonExistingInterface', false],
            'existing class'     => [ExistingClass::class, false],
            'existing trait'     => [ExistingTrait::class, false]
        ];
    }
}
