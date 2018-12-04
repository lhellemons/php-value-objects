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

    final protected function __construct($value)
    {
        self::assertValidValue($value);
        $this->value = self::normalizeValue($value);
    }

    final public static function of($value): self
    {
        return self::getInstance($value);
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
     * @param string|int|float|bool $value The value to normalize
     *
     * @return string|int|float|bool The normalized value
     */
    protected static function normalizeValue($value)
    {
        return $value;
    }

    /**
     * Validates the given value.
     * Override this method to provide specific validation for your class.
     * Throw a DomainException to indicate that the value is invalid.
     * If the value is valid, no action is necessary.
     *
     * @param string|int|float|bool $value
     * @throws \DomainException If the value is invalid
     */
    protected static function assertValidValue($value): void
    {
    }
}
