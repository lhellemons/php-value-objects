<?php

namespace Test\SolidPhp\ValueObjects\Type;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Type\TraitType;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingClass;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingInterface;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingTrait;

class TraitTypeTest extends TestCase
{
    /**
     * @dataProvider getCasesForFromFullyQualifiedTraitName
     * @param string $fullyQualifiedTraitName
     * @param bool $expectedResult
     */
    public function testFromFullyQualifiedTraitName(string $fullyQualifiedTraitName, bool $expectedResult): void
    {
        if ($expectedResult) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(InvalidArgumentException::class);
        }

        TraitType::fromFullyQualifiedTraitName($fullyQualifiedTraitName);
    }

    public function getCasesForFromFullyQualifiedTraitName(): array
    {
        return [
            'existing trait'     => [ExistingTrait::class, true],
            'non-existing trait' => ['NonExistingTrait', false],
            'existing class'     => [ExistingClass::class, false],
            'existing interface' => [ExistingInterface::class, false],
        ];
    }
}
