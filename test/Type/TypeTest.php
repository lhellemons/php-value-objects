<?php
/**
 * Created by PhpStorm.
 * User: lhell
 * Date: 2018-08-11
 * Time: 16:38
 */

namespace Test\SolidPhp\ValueObjects\Type;

use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Type\InterfaceType;
use SolidPhp\ValueObjects\Type\TraitType;
use SolidPhp\ValueObjects\Type\Kind;
use SolidPhp\ValueObjects\Type\Type;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingClass;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingInterface;
use Test\SolidPhp\ValueObjects\Type\TestObjects\ExistingTrait;

class TypeTest extends TestCase
{
    /**
     * @dataProvider getCasesForNamed
     * @param string $fullyQualifiedName
     * @param null|Kind $expectedKind
     */
    public function testNamed(string $fullyQualifiedName, ?Kind $expectedKind): void
    {
        if ($expectedKind) {
            $this->assertSame($expectedKind, Type::named($fullyQualifiedName)->getKind());
        } else {
            $this->expectException(\InvalidArgumentException::class);
            Type::named($fullyQualifiedName);
        }
    }

    public function getCasesForNamed(): array
    {
        return [
            'existing class'     => [ExistingClass::class, Kind::CLASS()],
            'existing interface' => [ExistingInterface::class, Kind::INTERFACE()],
            'existing trait'     => [ExistingTrait::class, Kind::TRAIT()],
            'non-existing name'  => ['NonExistingName', null],
        ];
    }

    /**
     * @dataProvider getCasesForOf
     * @param mixed $instance
     * @param Type $expectedType
     */
    public function testOf(object $instance, Type $expectedType): void
    {
        $this->assertSame($expectedType, Type::of($instance));
    }

    public function getCasesForOf(): array
    {
        return [
            'plain object' => [new ExistingClass(), Type::named(ExistingClass::class)],
            'anonymous class' => [$this->getAnonymousClassInstance(), Type::named(\get_class($this->getAnonymousClassInstance()))]
        ];
    }

    public function testIdentity(): void
    {
        $this->assertSame(
            ClassType::fromFullyQualifiedClassName(ExistingClass::class),
            ClassType::fromFullyQualifiedClassName(ExistingClass::class)
        );
        $this->assertSame(
            InterfaceType::fromFullyQualifiedInterfaceName(ExistingInterface::class),
            InterfaceType::fromFullyQualifiedInterfaceName(ExistingInterface::class)
        );
        $this->assertSame(
            TraitType::fromFullyQualifiedTraitName(ExistingTrait::class),
            TraitType::fromFullyQualifiedTraitName(ExistingTrait::class)
        );
    }

    private function getAnonymousClassInstance(): object
    {
        return new class() {};
    }
}
