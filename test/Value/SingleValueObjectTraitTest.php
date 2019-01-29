<?php

namespace Test\SolidPhp\ValueObjects\Value;

use PHPUnit\Framework\TestCase;
use SolidPhp\ValueObjects\Type\ClassType;
use SolidPhp\ValueObjects\Value\SingleValueObject;
use SolidPhp\ValueObjects\Value\SingleValueObjectInterface;
use SolidPhp\ValueObjects\Value\SingleValueObjectTrait;

class SingleValueObjectTraitTest extends TestCase
{
    /**
     * @dataProvider getCasesForOf
     *
     * @param ClassType   $valueObjectClass
     * @param string|int|float|bool      $source
     * @param string|int|float|bool|null $expectedValue
     */
    public function testOf(ClassType $valueObjectClass, $source, $expectedValue): void
    {
        if ($expectedValue === null) {
            $this->expectException(\Exception::class);
            $valueObjectClass->callStaticMethod('of', $source);
        } else {
            $this->assertSame($expectedValue, $valueObjectClass->callStaticMethod('of', $source)->getValue());
        }
    }

    public function getCasesForOf(): array
    {
        return [
            'simple (string)' => [ClassType::of(SimpleSingleValueObject::class), 'foo', 'foo'],
            'simple (int)' => [ClassType::of(SimpleSingleValueObject::class), 1, 1],
            'simple (float)' => [ClassType::of(SimpleSingleValueObject::class), 1.2, 1.2],
            'simple (bool)' => [ClassType::of(SimpleSingleValueObject::class), false, false],

            'normalized' => [ClassType::of(NormalizationSingleValueObject::class), ' FOO ', 'foo'],
            'normalized (type juggle)' => [ClassType::of(NormalizationSingleValueObject::class), 5, '5'],

            'validated (valid)' => [ClassType::of(ValidationSingleValueObject::class), 'valid', 'valid'],
            'validated (invalid)' => [ClassType::of(ValidationSingleValueObject::class), 'invalid', null],

            'inherited' => [ClassType::of(SingleValueObjectSubclass::class), 'value', 'value'],
        ];
    }

    /**
     * @dataProvider getCasesForGetValue
     *
     * @param SingleValueObjectInterface $singleValueObject
     * @param string                     $expectedValue
     */
    public function testGetValue(SingleValueObjectInterface $singleValueObject, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $singleValueObject->getValue());
    }

    public function getCasesForGetValue(): array
    {
        return [
            'simple' => [SimpleSingleValueObject::of('foo'), 'foo'],
            'normalized' => [NormalizationSingleValueObject::of(' FOO '), 'foo'],
            'validated (valid)' => [ValidationSingleValueObject::of('valid'), 'valid'],
            'inherited' => [SingleValueObjectSubclass::of('value'), 'value'],
        ];
    }
}

class SimpleSingleValueObject implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;
}

class ValidationSingleValueObject implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;

    protected static function validateRawValue($rawValue): void
    {
        if ($rawValue === 'invalid') {
            throw new \DomainException('Invalid!');
        }
    }
}

class NormalizationSingleValueObject implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;

    protected static function normalizeValidRawValue(string $validRawValue): string
    {
        return strtolower(trim($validRawValue));
    }
}

class SingleValueObjectSubclass extends SingleValueObject
{
}
