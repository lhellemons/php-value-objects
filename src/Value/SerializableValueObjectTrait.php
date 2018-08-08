<?php


namespace SolidPhp\ValueObjects\Value;

// TODO add PhpDoc
trait SerializableValueObjectTrait
{
    use ValueObjectTrait;

    final public function serialize(): string
    {
        return sprintf('%s:%s', static::class, json_encode($this->getValues()));
    }

    final public static function fromSerializedString(string $serializedString): self
    {
        [$serializedClass, $serializedValues] = explode(':', $serializedString, 2);

        if ($serializedClass !== static::class) {
            throw new \RuntimeException(
                sprintf(
                    'Invalid serialized value type (serialized type is %s, expected type is %s',
                    $serializedClass,
                    static::class
                )
            );
        }

        return static::fromValues(...array_values(json_decode($serializedValues, true)));
    }
}
