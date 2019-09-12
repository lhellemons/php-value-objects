<?php

namespace SolidPhp\ValueObjects\Value;

use SolidPhp\ValueObjects\Value\Ref\ValueObjectRepository;

/**
 * Trait SingleValueObjectTrait
 *
 * This trait gives the using class the ability to fulfill the contract for SingleValueObjectInterface without extra
 * code.
 * If / when your value object class starts to require normalization or validation, you can override
 * the default implementations of `normalizeValueString` and `assertValidValueString`
 *
 * @template T as scalar
 */
trait SingleValueObjectTrait /* implements SingleValueObjectInterface */
{
    /**
     * @var string|int|float|bool
     * @psalm-var T
     */
    private $value;

    /**
     * @param string|int|float|bool $value
     * @psalm-param T $value
     * @psalm-assert T $this->value
     */
    final protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param mixed $rawValue
     * @psalm-param T $rawValue
     *
     * @return object|static
     * @psalm-return static<T>
     */
    final public static function of($rawValue): self
    {
        static::validateRawValue($rawValue);

        /** @psalm-var static<T> */
        return ValueObjectRepository::getInstanceOfClass(static::class, static::normalizeValidRawValue($rawValue));
    }

    /**
     * @return bool|float|int|string
     * @psalm-return T
     */
    final public function getValue()
    {
        /** @psalm-var T */
        return $this->value;
    }

    final public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * Normalizes the value string.
     * Override this method to provide specific normalization for your class
     *
     * @param string|int|float|bool $validRawValue The value to normalize
     * @psalm-param T $validRawValue
     *
     * @return string|int|float|bool The normalized value
     * @psalm-return T
     */
    protected static function normalizeValidRawValue($validRawValue)
    {
        return $validRawValue;
    }

    /**
     * Validates the given value.
     * Override this method to provide specific validation for your class.
     * Throw a DomainException to indicate that the value is invalid.
     * If the value is valid, simply return without throwing.
     *
     * @param string|int|float|bool $rawValue
     * @psalm-param T $rawValue
     *
     * @throws \DomainException If the value is invalid
     */
    protected static function validateRawValue($rawValue): void
    {
    }
}
