<?php


namespace SolidPhp\ValueObjects\Value;

trait ValueObjectTrait /* implements ValueObjectInterface */
{
    protected static $instanceProperties;

    protected static $instances;

    final private function __construct(array $valueArray)
    {
        self::ensureInstancePropertiesAnalyzed();

        if ($missingPropValues = array_diff(self::$instanceProperties, array_keys($valueArray))) {
            throw new \RuntimeException(
                sprintf(
                    'Value type %s instantiation missing property value%s for %s',
                    static::class,
                    count($missingPropValues) > 1 ? 's': '',
                    implode(', ', $missingPropValues))
            );
        }

        foreach (static::$instanceProperties as $property) {
            $this->$property = $valueArray[$property];
        }
    }

    final protected static function fromValues(...$values): self
    {
        static::ensureInstancePropertiesAnalyzed();

        return static::fromPropertyValues(array_combine(self::$instanceProperties, $values));
    }

    final protected static function fromPropertyValues(array $propertyValues): self
    {
        $key = get_value_object_instance_key($propertyValues);
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new static($propertyValues);
        }

        return static::$instances[$key];
    }

    public function equals(ValueObjectInterface $object): bool
    {
        return $object === $this;
    }

    final protected function getValues(): array
    {
        static::ensureInstancePropertiesAnalyzed();

        return array_combine(
            static::$instanceProperties,
            array_map(
                function (string $instanceProperty) {
                    return $this->{$instanceProperty};
                },
                static::$instanceProperties
            )
        );
}

private static function ensureInstancePropertiesAnalyzed(): void
{
    if (static::$instanceProperties === null) {
        static::$instanceProperties = analyze_instance_properties(static::class);
    }
}
}

function analyze_instance_properties(string $valueObjectClass): array
{
    try {
        $reflectionClass = new \ReflectionClass($valueObjectClass);

        return array_map(
            function (\ReflectionProperty $instanceProperty) {
                return $instanceProperty->getName();
            },
            array_filter(
                $reflectionClass->getProperties(),
                'SolidPhp\ValueObjects\Value\is_value_object_instance_property'
            )
        );
    } catch (\ReflectionException $e) {
        throw new \DomainException(
            sprintf('Unable to analyze instance properties for value object type %s', $valueObjectClass), 0, $e
        );
    }
}

function is_value_object_instance_property(\ReflectionProperty $property): bool
{
    return !$property->isStatic() && !$property->isPublic();
}

function get_value_object_instance_key(array $values): string
{
    ksort($values);
    return json_encode($values);
}
