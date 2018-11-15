<?php

namespace Test\SolidPhp\ValueObjects\Enum\EnumTrait;

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

    public function testInheritance(): void
    {
        $this->assertSame(TestInheritanceParentEnum::PARENT(), TestInheritanceParentEnum::PARENT());
        $this->assertSame(TestInheritanceChildEnum::PARENT(), TestInheritanceChildEnum::PARENT());
        $this->assertNotSame(TestInheritanceParentEnum::PARENT(), TestInheritanceChildEnum::PARENT());
        $this->assertNotSame(TestInheritanceParentEnum::PARENT(), TestInheritanceChildEnum::PARENT());
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

    protected function __construct(string $id, string $message)
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


class TestInheritanceParentEnum implements EnumInterface
{
    use EnumTrait;

    /** @var string */
    private $parentProp;

    protected function __construct(string $id, string $parentProp)
    {
        $this->parentProp = $parentProp;
    }

    public static function PARENT(): self
    {
        return self::define('PARENT', 'parentPropValue');
    }

    public function getParentProp()
    {
        return $this->parentProp;
    }
}

class TestInheritanceChildEnum extends TestInheritanceParentEnum
{
    /** @var null|string */
    private $childProp;

    protected function __construct(string $id, string $parentPropValue, ?string $childPropValue = null)
    {
        parent::__construct($id, $parentPropValue);
        $this->childProp = $childPropValue;
    }

    public static function CHILD(): self
    {
        return self::define('CHILD', 'parentPropValue', 'childPropValue');
    }

    public function getChildProp()
    {
        return $this->childProp;
    }
}
