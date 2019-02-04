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
            'normalized inherited' => [ClassType::of(NormalizationSingleValueObjectSubclass::class), ' FOO ', 'foo-normalizedBySubclass'],

            'validated (valid)' => [ClassType::of(ValidationSingleValueObject::class), 'valid', 'valid'],
            'validated (invalid)' => [ClassType::of(ValidationSingleValueObject::class), 'invalid', null],
            'validated inherited (invalid in parent)' => [ClassType::of(ValidationSingleValueObjectSubclass::class), 'invalid', null],
            'validated inherited (invalid in child)' => [ClassType::of(ValidationSingleValueObjectSubclass::class), 'invalidInSubclass', null],

            'inherited' => [ClassType::of(SingleValueObjectSubclass::class), 'value', 'value'],
        ];
    }

    /**
     * @dataProvider getCasesForSame
     * @param      $valueObjectA
     * @param      $valueObjectB
     * @param bool $expectedSame
     */
    public function testSame($valueObjectA, $valueObjectB, bool $expectedSame): void
    {
        if ($expectedSame) {
            $this->assertSame($valueObjectA, $valueObjectB);
            } else {
            $this->assertNotSame($valueObjectA, $valueObjectB);
        }
    }

    public function getCasesForSame(): array
    {
        return [
            'simple (string) - equal' => [SimpleSingleValueObject::of('foo'), SimpleSingleValueObject::of('foo'), true],
            'simple (string) - not equal' => [SimpleSingleValueObject::of('foo'), SimpleSingleValueObject::of('bar'), false],

            'simple (int) - equal' => [SimpleSingleValueObject::of(1), SimpleSingleValueObject::of(1), true],
            'simple (int) - not equal' => [SimpleSingleValueObject::of(1), SimpleSingleValueObject::of(2), false],

            'simple (float) - equal' => [SimpleSingleValueObject::of(1.0), SimpleSingleValueObject::of(1.0), true],
            'simple (float) - not equal' => [SimpleSingleValueObject::of(1), SimpleSingleValueObject::of(1.1), false],

            'simple (bool) - equal' => [SimpleSingleValueObject::of(true), SimpleSingleValueObject::of(true), true],
            'simple (bool) - not equal' => [SimpleSingleValueObject::of(true), SimpleSingleValueObject::of(false), false],

            'array - equal' => [SimpleSingleValueObject::of(['foo']), SimpleSingleValueObject::of(['foo']), true],
            'array - different values' => [SimpleSingleValueObject::of(['foo']), SimpleSingleValueObject::of(['bar']), false],
            'array - different keys' => [SimpleSingleValueObject::of([1 => 'foo']), SimpleSingleValueObject::of([2 => 'foo']), false],
            'array - different length' => [SimpleSingleValueObject::of(['foo']), SimpleSingleValueObject::of(['foo', 'bar']), false],

            'normalized - equal' => [NormalizationSingleValueObject::of('foo'), NormalizationSingleValueObject::of(' FOO '), true],
            'normalized - not equal' => [NormalizationSingleValueObject::of('foo'), NormalizationSingleValueObject::of('bar'), false],
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

class ValidationSingleValueObjectSubclass extends ValidationSingleValueObject
{
    protected static function validateRawValue($rawValue): void
    {
        parent::validateRawValue($rawValue);

        if ($rawValue === 'invalidInSubclass') {
            throw new \DomainException('Invalid!');
        }
    }
}

class NormalizationSingleValueObjectSubclass extends NormalizationSingleValueObject
{
    protected static function normalizeValidRawValue(string $validRawValue): string
    {
        return parent::normalizeValidRawValue($validRawValue) . '-normalizedBySubclass';
    }

}

class ValidationSingleValueObjectNormalizationSubclass extends ValidationSingleValueObject
{
    protected static function normalizeValidRawValue($validRawValue)
    {
        return parent::normalizeValidRawValue($validRawValue) . '-normalizedBySubclass';
    }
}


class NormalizationSingleValueObjectValidationSubclass extends NormalizationSingleValueObject
{
    protected static function validateRawValue($rawValue): void
    {
        if ($rawValue === 'invalidInSubclass') {
            throw new \DomainException('Invalid!');
        }
    }
}
