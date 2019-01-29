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
     * @dataProvider getCasesForOfString
     *
     * @param string $fullyQualifiedName
     * @param null|Kind $expectedKind
     */
    public function testOfString(string $fullyQualifiedName, ?Kind $expectedKind): void
    {
        if ($expectedKind) {
            $this->assertSame($expectedKind, Type::of($fullyQualifiedName)->getKind());
        } else {
            $this->expectException(\InvalidArgumentException::class);
            Type::of($fullyQualifiedName);
        }
    }

    public function getCasesForOfString(): array
    {
        return [
            'existing class'     => [ExistingClass::class, Kind::CLASS()],
            'existing interface' => [ExistingInterface::class, Kind::INTERFACE()],
            'existing trait'     => [ExistingTrait::class, Kind::TRAIT()],
            'non-existing name'  => ['NonExistingName', null],
        ];
    }

    /**
     * @dataProvider getCasesForOfObject
     *
     * @param mixed $source
     * @param Type  $expectedType
     */
    public function testOfObject($source, ?Type $expectedType): void
    {
        if ($expectedType === null) {
            $this->expectException(\InvalidArgumentException::class);
            Type::of($source);
        } else {
            $this->assertSame($expectedType, Type::of($source));
        }
    }

    public function getCasesForOfObject(): array
    {
        return [
            'plain object'    => [new ExistingClass(), Type::of(ExistingClass::class)],
            'anonymous class' => [
                $this->getAnonymousClassInstance(),
                Type::of(\get_class($this->getAnonymousClassInstance()))
            ]
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

    /**
     * @dataProvider getCasesForIsSuperTypeOf
     * @param Type $type
     * @param Type $otherType
     * @param bool $expectedResult
     */
    public function testIsSuperTypeOf(Type $type, Type $otherType, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $type->isSupertypeOf($otherType));
    }

    public function getCasesForIsSuperTypeOf(): array
    {
        $class = Type::of(TestClass::class);
        $trait = Type::of(TestTrait::class);
        $interface = Type::of(TestInterface::class);
        $subclass = Type::of(TestSubclass::class);
        $classImplementsInterface = Type::of(TestClassImplementsTestInterface::class);
        $subclassImplementsInterface = Type::of(TestSubclassImplementsTestInterface::class);
        $classUsesTrait = Type::of(TestClassUsesTestTrait::class);
        $subclassUsesTrait = Type::of(TestSubclassUsesTestTrait::class);

        return [
            'same class'            => [$class, $class, true],
            'same interface'        => [$interface, $interface, true],
            'same trait'            => [$trait, $trait, true],
            'subclass'              => [$class, $subclass, true],
            'implements'            => [$interface, $classImplementsInterface, true],
            'transitive implements' => [$interface, $subclassImplementsInterface, true],

            'uses'            => [$trait, $classUsesTrait, false],
            'transitive uses' => [$trait, $subclassUsesTrait, false],
        ];
    }

    /**
     * @dataProvider getCasesForIsSubTypeOf
     * @param Type $type
     * @param Type $otherType
     * @param bool $expectedResult
     */
    public function testIsSubTypeOf(Type $type, Type $otherType, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $type->isSubtypeOf($otherType));
    }

    public function getCasesForIsSubTypeOf(): array
    {
        $class = Type::of(TestClass::class);
        $trait = Type::of(TestTrait::class);
        $interface = Type::of(TestInterface::class);
        $subclass = Type::of(TestSubclass::class);
        $classImplementsInterface = Type::of(TestClassImplementsTestInterface::class);
        $subclassImplementsInterface = Type::of(TestSubclassImplementsTestInterface::class);
        $classUsesTrait = Type::of(TestClassUsesTestTrait::class);
        $subclassUsesTrait = Type::of(TestSubclassUsesTestTrait::class);

        return [
            'same class'            => [$class, $class, true],
            'same interface'        => [$interface, $interface, true],
            'same trait'            => [$trait, $trait, true],
            'subclass'              => [$subclass, $class, true],
            'implements'            => [$classImplementsInterface, $interface, true],
            'transitive implements' => [$subclassImplementsInterface, $interface, true],

            'uses'            => [$classUsesTrait, $trait, false],
            'transitive uses' => [$subclassUsesTrait, $trait, false],
        ];
    }

    /**
     * @dataProvider getCasesForIsInstance
     * @param Type $type
     * @param mixed $object
     * @param bool $expectedResult
     */
    public function testIsInstance(Type $type, $object, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $type->isInstance($object));
    }

    public function getCasesForIsInstance(): array
    {
        $class = Type::of(TestClass::class);
        $trait = Type::of(TestTrait::class);
        $interface = Type::of(TestInterface::class);
        $subclass = Type::of(TestSubclass::class);
        $classUsesTrait = Type::of(TestClassUsesTestTrait::class);
        $subclassUsesTrait = Type::of(TestSubclassUsesTestTrait::class);

        return [
            'class'                  => [$class, new TestClass(), true],
            'interface'              => [$interface, new TestClassImplementsTestInterface(), true],
            'subclass'               => [$subclass, new TestSubclass(), true],
            'subclass -> superclass' => [$class, new TestSubclass(), true],
            'class -> interface'     => [$interface, new TestClassImplementsTestInterface(), true],
            'subclass -> interface'  => [$interface, new TestSubclassImplementsTestInterface(), true],

            'trait'             => [$trait, new TestClassUsesTestTrait(), false],
            'class -> trait'    => [$trait, new TestClassUsesTestTrait(), false],
            'subclass -> trait' => [$trait, new TestSubclassUsesTestTrait(), false],

            'uses'            => [$classUsesTrait, $trait, false],
            'transitive uses' => [$subclassUsesTrait, $trait, false],
        ];
    }

    private function getAnonymousClassInstance()
    {
        return new class()
        {
        };
    }
}

trait TestTrait
{
}

interface TestInterface
{
}

class TestClass
{
}

class TestClassImplementsTestInterface implements TestInterface
{
}

class TestClassUsesTestTrait
{
    use TestTrait;
}

class TestSubclass extends TestClass
{
}

class TestSubclassImplementsTestInterface extends TestClassImplementsTestInterface
{
}

class TestSubclassUsesTestTrait extends TestClassUsesTestTrait
{
}
