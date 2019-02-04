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

    public function testNoConstructor(): void
    {
        $this->expectException(\LogicException::class);
        NoConstructorType::fromFoo('foo');
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
}

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

class NoConstructorType
{
    use ValueObjectTrait;

    public static function fromFoo(string $foo)
    {
        return self::getInstance($foo);
    }
}
