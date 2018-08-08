<?php


namespace SolidPhp\ValueObjects\Value;

trait ValueObjectTrait /* implements ValueObjectInterface */
{
    private static $instanceProperties;

    private static $instances;

    final private function __construct(...$values)
    {
        self::ensureInstancePropertiesAnalyzed();

        if (($parameterCount = \count($values)) !== ($instancePropertyCount = \count(static::$instanceProperties))) {
            throw new \DomainException(
                sprintf(
                    'Unable to instantiate value object of type %s; %d parameters expected, %d given',
                    static::class,
                    $instancePropertyCount,
                    $parameterCount
                )
            );
        }

        foreach (array_combine(self::$instanceProperties, $values) as $property => $value) {
            $this->$property = $value;
        }
    }

    final protected static function fromValues(...$values): self
    {
        $key = get_value_object_instance_key($values);
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new static(...$values);
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
    return !$property->isStatic() && $property->isPrivate();
}

function get_value_object_instance_key(array $values): string
{
    return json_encode($values);
}
