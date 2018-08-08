<?php

namespace Test\SolidPhp\ValueObjects\Value;

use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Enum\EnumInterface;
use SolidPhp\ValueObjects\Enum\EnumTrait;

class EnumTraitTest extends TestCase
{
    public function testInstances(): void
    {
        $this->assertEquals([TestInstancesEnum::ONE(), TestInstancesEnum::TWO()], TestInstancesEnum::instances());
    }

    public function testFactoryMethods(): void
    {
        $this->assertSame(TestFactoryMethodsEnum::ONE(),TestFactoryMethodsEnum::ONE());
        $this->assertSame(TestFactoryMethodsEnum::TWO(),TestFactoryMethodsEnum::TWO());
        $this->assertNotSame(TestFactoryMethodsEnum::ONE(),TestFactoryMethodsEnum::TWO());
    }

    public function testInstance(): void
    {
        $this->assertSame(TestInstanceEnum::ONE(), TestInstanceEnum::instance('one'));
        $this->assertSame(TestInstanceEnum::TWO(), TestInstanceEnum::instance('two'));
        $this->assertNull(TestInstanceEnum::instance('three'));

        $this->expectException(\DomainException::class);
        TestInstanceEnum::instance('three',true);
    }

    public function testGetId(): void
    {
        $this->assertEquals('one', TestGetIdEnum::instance('one')->getId());
        $this->assertEquals('two', TestGetIdEnum::instance('two')->getId());
    }

    public function testInitialize(): void
    {
        $this->assertEquals('eno', TestInitializeEnum::ONE()->getMessage());
        $this->assertEquals('owt', TestInitializeEnum::TWO()->getMessage());
    }
}

class TestInstancesEnum implements EnumInterface
{
    use EnumTrait;

    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestFactoryMethodsEnum implements EnumInterface
{
    use EnumTrait;

    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestInstanceEnum implements EnumInterface
{
    use EnumTrait;

    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestGetIdEnum implements EnumInterface
{
    use EnumTrait;

    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}


class TestInitializeEnum implements EnumInterface
{
    use EnumTrait;

    /** @var string */
    private $message;

    protected function initialize(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public static function ONE(): self
    {
        return self::define('one', 'eno');
    }

    public static function TWO(): self
    {
        return self::define('two', 'owt');
    }
}
