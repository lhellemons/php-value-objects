<?php

namespace SolidPhp\ValueObjects\Value;

/**
 * Trait SingleValueObjectTrait
 *
 * This trait gives the using class the ability to fulfill the contract for SingleValueObjectInterface without extra
 * code.
 * If / when your value object class starts to require normalization or validation, you can override
 * the default implementations of `normalizeValueString` and `assertValidValueString`
 */
trait SingleValueObjectTrait /* implements SingleValueObjectInterface */
{
    use ValueObjectTrait;

    /** @var string */
    private $value;

    final protected function __construct($rawValue)
    {
        static::validateRawValue($rawValue);
        $this->value = static::normalizeValidRawValue($rawValue);
    }

    final public static function of($rawValue): self
    {
        return self::getInstance($rawValue);
    }

    final public function getValue()
    {
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
     *
     * @return string|int|float|bool The normalized value
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
     *
     * @throws \DomainException If the value is invalid
     */
    protected static function validateRawValue($rawValue): void
    {
    }
}
