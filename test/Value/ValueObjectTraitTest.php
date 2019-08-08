<?php

namespace Test\SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Value\ValueObjectException;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;
use PHPUnit\Framework\TestCase;

class ValueObjectTraitTest extends TestCase
{
    public function testFromValues(): void
    {
        $a1 = FromValuesType::fromFooAndBar('a', 1);
        $a2 = FromValuesType::fromFooAndBar('a', 2);
        $b1 = FromValuesType::fromFooAndBar('b', 1);

        $this->assertSame($a1, $a1);
        $this->assertSame($a2, $a2);
        $this->assertSame($b1, $b1);

        $this->assertNotSame($a1, $a2);
        $this->assertNotSame($a2, $b1);
        $this->assertNotSame($a1, $b1);
    }

    public function testGetters(): void
    {
        $a1 = FromValuesType::fromFooAndBar('a', 1);
        $a2 = FromValuesType::fromFooAndBar('a', 2);
        $b1 = FromValuesType::fromFooAndBar('b', 1);

        $this->assertEquals('a',$a1->getFoo());
        $this->assertEquals('1',$a1->getBar());

        $this->assertEquals('a',$a2->getFoo());
        $this->assertEquals('2',$a2->getBar());

        $this->assertEquals('b',$b1->getFoo());
        $this->assertEquals('1',$b1->getBar());
    }

    public function testInheritance(): void
    {
        $superclass1 = SuperclassType::fromFoo('1');
        $subclassA1 = SubclassAType::fromFoo('1');
        $subclassB11 = SubclassBType::fromFooAndBar('1', 1);
        $subclassC11 = SubclassCType::fromFooAndBar('1',1);

        $this->assertSame($superclass1, SuperclassType::fromFoo('1'));
        $this->assertSame($subclassA1, SubclassAType::fromFoo('1'));
        $this->assertSame($subclassB11, SubclassBType::fromFooAndBar('1', 1));
        $this->assertSame($subclassC11, SubclassCType::fromFooAndBar('1', 1));

        $this->assertNotSame($superclass1, $subclassA1);
        $this->assertNotSame($subclassA1,$subclassB11);
        $this->assertNotSame($subclassA1,$subclassC11);
        $this->assertNotSame($subclassB11,$subclassC11);
    }

    public function testInheritanceWithoutParentConstructor(): void
    {
        try {
            SubclassWithConstructorType::fromFoo('1');
            $this->expectNotToPerformAssertions();
        } catch (\Throwable $e) {
            $this->fail(sprintf('Unable to instantiate value object subclass without parent constructor: %s', $e));
        }
    }

    public function testNoMutation(): void
    {
        $testObject = FromValuesType::fromFooAndBar('foo', 1);

        try {
            $var = $testObject->foo;
            $this->fail('Able to get a private property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $var = $testObject->nonExistentProperty;
            $this->fail('Able to get a non-existent property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $var = isset($testObject->foo);
            $this->fail('Able to query existence of a private property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $var = isset($testObject->nonExistentProperty);
            $this->fail('Able to query existence of a private property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $testObject->foo = 'newValue';
            $this->fail('Able to set a private property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $testObject->nonExistentProperty = 'newValue';
            $this->fail('Able to set a new property on value object type');
        } catch (ValueObjectException $e) {}

        try {
            $clonedTestObject = clone $testObject;
            $this->fail('Able to clone a value object');
        } catch (ValueObjectException $e) {}

        try {
            $serializedTestObject = serialize($testObject);
            $unserializedTestObject = unserialize($serializedTestObject);
            $this->fail('Able to unserialize a value object');
        } catch (ValueObjectException $e) {}

        try {
            $exportedTestObject = var_export($testObject, true);
            $unserializedTestObject = eval($exportedTestObject . ';');
            $this->fail('Able to import a var_exported value object');
        } catch (ValueObjectException $e) {}

        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider getCasesForValueTypes
     * @param      $firstValue
     * @param      $secondValue
     * @param bool $expectedSame
     */
    public function testValueTypes($firstValue, $secondValue, bool $expectedSame): void
    {
        $instanceA = ValueTypesType::of($firstValue);
        $instanceB = ValueTypesType::of($secondValue);

        if ($expectedSame) {
            $this->assertSame($instanceA, $instanceB);
        } else {
            $this->assertNotSame($instanceA, $instanceB);
        }
    }

    public function getCasesForValueTypes(): array
    {
        $stdClassA = new \stdClass();
        $stdClassA->foo = 'foo';
        $stdClassB = new \stdClass();
        $stdClassB->foo = 'bar';

        $objectA = new ObjectClass();
        $objectB = new ObjectClass();

        $resourceA = fopen(__DIR__ . '/test_resource', 'r');
        $resourceB = fopen(__DIR__ . '/test_resource', 'r');

        return [
            'boolean - equal' => [true, true, true],
            'boolean - not equal' => [true, false, false],
            'int - equal' => [1,1,true],
            'int - not equal' => [1,2,false],
            'float - equal' => [1.0, 1.0, true],
            'float - not equal' => [1.0, 1.1, false],
            'string - equal' => ['foo', 'foo', true],
            'string - not equal' => ['foo', 'bar', false],
            'array - empty equal' => [[], [], true],
            'array - equal values' => [['a'],['a'], true],
            'array - different values' => [['a'],['b'], false],
            'array - different keys' => [[1 => 'a'],[2 => 'a'], false],

            'stdClass - same instance' => [$stdClassA, $stdClassA, true],
            'stdClass - different instances' => [$stdClassA, $stdClassB, false],
            'stdClass - different literals' => [new \stdClass(), new \stdClass(), true],

            'object - same instance' => [$objectA, $objectA, true],
            'object - different instance' => [$objectA, $objectB, false],

            'resource - same instance' => [$resourceA, $resourceA, true],
            'resource - different instance' => [$resourceA, $resourceB, false],

            'boolean / int' => [true, 1, false],
            'boolean / float' => [true, 1.0, false],
            'boolean / string' => [true, '1', false],
            'boolean / array' => [true, [1], false],
            'boolean / resource' => [true, $resourceA, false],
            'boolean / object' => [true, $objectA, false],
            'boolean / stdClass' => [true, new \stdClass(), false],

            'int / float' => [1, 1.0, false],
            'int / string' => [1, '1', false],
            'int / array' => [1, [1], false],
            'int / resource' => [1, $resourceA, false],
            'int / object' => [1, $objectA, false],
            'int / stdClass' => [1, new \stdClass(), false],

            'float / string' => [1.0, '1.0', false],
            'float / array' => [1.0, [1.0], false],
            'float / resource' => [1.0, $resourceA, false],
            'float / object' => [1.0, $objectA, false],
            'float / stdClass' => [1.0, new \stdClass(), false],

            'string / array' => ['foo', ['foo'], false],
            'string / resource' => ['foo', $resourceA, false],
            'string / object' => ['foo', $objectA, false],
            'string / stdClass' => ['foo', new \stdClass(), false],

            'array / resource' => [[1], $resourceA, false],
            'array / object' => [[1], $objectA, false],

            'resource / object' => [$resourceA, $objectA, false],
            'resource / stdClass' => [$resourceA, $stdClassA, false],

            'array / empty stdClass' => [[], new \stdClass(), true],
            'array / non-empty stdClass' => [['foo'], (object)['foo'], true],
            'array / different non-empty stdClass' => [['foo'], (object)['bar'], false],
        ];
    }

    /**
     * @param       $instance
     * @param array $expectedResult
     * @dataProvider getCasesForComponents
     */
    public function testComponents($instance, array $expectedResult): void
    {
        $this->assertEquals($expectedResult, $instance->components());
    }

    public function getCasesForComponents(): array
    {
        return [
            '' => [ValueTypesType::of('value'), ['value' => 'value']],
        ];
    }

    /**
     * @param        $instance
     * @param string $expectedResult
     *
     * @dataProvider getCasesForToString
     */
    public function testToString($instance, string $expectedResult): void
    {
        $this->assertEquals($expectedResult, (string)$instance);
    }

    public function getCasesForToString(): array
    {
        return [
            'constructor' => [ValueTypesType::of('value'), "ValueTypesType(value='value')"],
            'no constructor' => [ValueTypesWithoutConstructorType::of('value'), "ValueTypesWithoutConstructorType('value')"]
        ];
    }
}

class ValueTypesType
{
    use ValueObjectTrait;

    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function of($value): self
    {
        return self::getInstance($value);
    }

    public function getValue()
    {
        return $this->value;
    }
}

class ValueTypesWithoutConstructorType
{
    use ValueObjectTrait;

    public static function of($value): self
    {
        return self::getInstance($value);
    }
}

class ObjectClass {}

class FromValuesType
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    /** @var int */
    private $bar;

    private function __construct(string $foo, int $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return self::getInstance($foo, $bar);
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}

class GettersType
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    /** @var int */
    private $bar;

    public function __construct(string $foo, int $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return self::getInstance($foo, $bar);
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}

class SuperclassType
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    protected function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public static function fromFoo(string $foo): self
    {
        return self::getInstance($foo);
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}

class SubclassAType extends SuperclassType
{
}

class SubclassBType extends SuperclassType
{
    /** @var int */
    private $barB;

    protected function __construct(string $foo, int $barB)
    {
        parent::__construct($foo);
        $this->barB = $barB;
    }

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return static::getInstance($foo, $bar);
    }

    public function getBarB(): int
    {
        return $this->barB;
    }
}

class SubclassCType extends SuperclassType
{
    /** @var int */
    private $barC;

    protected function __construct(string $foo, int $barC)
    {
        parent::__construct($foo);
        $this->barC = $barC;
    }

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return static::getInstance($foo, $bar);
    }

    public function getBarC(): int
    {
        return $this->barC;
    }
}



abstract class SuperclassWithoutConstructorType
{
    use ValueObjectTrait;
}

class SubclassWithConstructorType extends SuperclassWithoutConstructorType
{
    /** @var string */
    private $foo;

    protected function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public static function fromFoo(string $foo): self
    {
        return self::getInstance($foo);
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}
