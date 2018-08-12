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

class TypeTest extends TestCase
{
    /**
     * @dataProvider   getCasesForExistingType
     *
     * @param string $classString
     * @param Kind   $kind
     */
    public function testExistingType(string $classString, Kind $kind): void
    {
        $type = null;
        switch ($kind) {
            case Kind::INTERFACE():
                $type =InterfaceType::fromClassString($classString);
                break;
            case Kind::TRAIT():
                $type = TraitType::fromClassString($classString);
                break;
            case Kind::CLASS():
                $type = ClassType::fromClassString($classString);
                break;
        }

        $this->assertEquals($classString, $type->getName());
        $this->assertEquals($kind, $type->getKind());
    }

    public function getCasesForExistingType(): array
    {
        return [
            'ExistingClass' => [ExistingClass::class, Kind::CLASS()],
            'ExistingInterface' => [ExistingInterface::class, Kind::INTERFACE()],
            'ExistingTrait' => [ExistingTrait::class, Kind::TRAIT()],
        ];
    }

    /**
     * @dataProvider getCasesForNonExistingType
     *
     * @param string $classString
     * @param Kind   $kind
     */
    public function testNonExistingType(string $classString, Kind $kind): void
    {
        $this->expectException(\RuntimeException::class);

        switch ($kind) {
            case Kind::INTERFACE():
                InterfaceType::fromClassString($classString);
                break;
            case Kind::TRAIT():
                TraitType::fromClassString($classString);
                break;
            case Kind::CLASS():
                ClassType::fromClassString($classString);
                break;
        }
    }

    public function getCasesForNonExistingType(): array
    {
        return [
            'non-existing class' => ['NonExistingClass', Kind::CLASS()],
            'non-existing interface' => ['NonExistingInterface', Kind::INTERFACE()],
            'non-existing trait' => ['NonExistingTrait', Kind::TRAIT()],
            'class as interface' => [ExistingClass::class, Kind::INTERFACE()],
            'class as trait' => [ExistingClass::class, Kind::TRAIT()],
            'interface as class' => [ExistingInterface::class, Kind::CLASS()],
            'interface as trait' => [ExistingInterface::class, Kind::TRAIT()],
            'trait as interface' => [ExistingTrait::class, Kind::INTERFACE()],
            'trait as class' => [ExistingTrait::class, Kind::CLASS()],
        ];
    }
}


interface ExistingInterface {}
trait ExistingTrait {}
class ExistingClass implements ExistingInterface {}
