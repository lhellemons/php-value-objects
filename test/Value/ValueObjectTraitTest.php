<?php

namespace Test\SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Value\ValueObjectInterface;
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
}

class FromValuesType implements ValueObjectInterface
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    /** @var int */
    private $bar;

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return self::fromValues($foo, $bar);
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

class GettersType implements ValueObjectInterface
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    /** @var int */
    private $bar;

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return self::fromValues($foo, $bar);
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
