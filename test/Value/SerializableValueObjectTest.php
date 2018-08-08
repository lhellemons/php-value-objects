<?php

namespace Test\SolidPhp\ValueObjects\Value;

use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Value\SerializableValueObjectTrait;
use SolidPhp\ValueObjects\Value\ValueObjectInterface;

class SerializableValueObjectTest extends TestCase
{
    public function testSerialize(): void
    {
        $valueObject = SerializeType::fromValue('foo');
        $otherValueObject = SerializeType::fromValue('bar');

        $serializedValue = $valueObject->serialize();
        $otherSerializedValue = $otherValueObject->serialize();

        $deserializedValueObject = SerializeType::fromSerializedString($serializedValue);
        $otherDeserializedValueObject = SerializeType::fromSerializedString($otherSerializedValue);

        $this->assertSame($valueObject,$deserializedValueObject);
        $this->assertSame($otherValueObject, $otherDeserializedValueObject);
        $this->assertNotSame($deserializedValueObject, $otherDeserializedValueObject);
    }

    // TODO add unit test for value objects / enums as value
}

class SerializeType implements ValueObjectInterface
{
    use SerializableValueObjectTrait;

    /** @var string */
    private $value;

    public static function fromValue($value): self
    {
        return static::fromValues($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
