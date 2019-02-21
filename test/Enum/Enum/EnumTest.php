<?php

namespace Test\SolidPhp\ValueObjects\Enum\Enum;

use SolidPhp\ValueObjects\Enum\Enum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
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
        $this->assertEquals('eno', TestCustomConstructorEnum::ONE()->getMessage());
        $this->assertEquals('owt', TestCustomConstructorEnum::TWO()->getMessage());
    }

    public function testInheritance(): void
    {
        $this->assertSame(TestInheritanceParentEnum::PARENT(), TestInheritanceParentEnum::PARENT());
        $this->assertSame(TestInheritanceChildEnum::PARENT(), TestInheritanceChildEnum::PARENT());
        $this->assertNotSame(TestInheritanceParentEnum::PARENT(), TestInheritanceChildEnum::PARENT());
        $this->assertNotSame(TestInheritanceParentEnum::PARENT(), TestInheritanceChildEnum::PARENT());
    }

    public function testDefineInstances(): void
    {
        $this->assertSame(TestCustomDefineInstances::instances(), [TestCustomDefineInstances::instance('FOO'), TestCustomDefineInstances::instance('BAR')]);
    }
}

class TestInstancesEnum extends Enum
{
    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestFactoryMethodsEnum extends Enum
{
    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestInstanceEnum extends Enum
{
    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}

class TestGetIdEnum extends Enum
{
    public static function ONE(): self
    {
        return self::define('one');
    }

    public static function TWO(): self
    {
        return self::define('two');
    }
}


class TestCustomConstructorEnum extends Enum
{
    /** @var string */
    private $message;

    protected function __construct(string $id, string $message)
    {
        parent::__construct($id);
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

class TestInheritanceParentEnum extends Enum
{
    /** @var string */
    private $parentProp;

    protected function __construct(string $id, string $parentProp)
    {
        parent::__construct($id);
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

final class TestCustomDefineInstances extends Enum
{
    public const FOO = 'FOO';
    public const BAR = 'BAR';

    protected static function defineInstances(): void
    {
        self::define(self::FOO);
        self::define(self::BAR);
    }
}
