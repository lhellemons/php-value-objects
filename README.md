SolidPhp Value Objects library
==============================

Author: Laurens Hellemons <lhellemons@gmail.com>

This library contains utility classes, traits and interfaces for working with value objects in PHP.
"Value objects" are defined as objects that are defined by the value of their properties
(as opposed to "entities", which have an identity that remains the same even if their properties change).
Value objects are defined as equal if their properties are equal.

PHP has a number of features that make working with value objects difficult to do natively, namely:
- There is no suitable native operator to test for value object equality: The equality-by-value operator "=="
  does implicit type conversion, which yields false positives, for example when comparing with a variable
  that happens to be boolean true. The equality-by-identity operator "===" only yields true if the two operands
  are literally the same object instance.



Value object
------------

The Value namespace contains several useful classes that will help you use value objects in your project.
As said, a value object is one that is defined solely by its type and the values of its properties.
You can convert a class to a value object in two ways:
- By implementing ValueObjectInterface and using ValuObjectTrait
- By extending the ValueObject base class, which does this for you

You can then add instance properties and static factory methods to your class.
The factory methods should validate and normalize their parameters, and then call `static::fromValues`
with the parameters. The order of the arguments to `static::fromValues` must match the order in which the
instance properties are defined!

Example:
```
class FromValuesType implements ValueObjectInterface
{
    use ValueObjectTrait;

    /** @var string */
    private $foo;

    /** @var int */
    private $bar;

    public static function fromFooAndBar(string $foo, int $bar): self
    {
        return self::fromValues($foo, $bar);
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}

```

Important things to remember:
- Never mutate the value of your instance properties! You can add non-static factory methods that
 use `static::fromValues` to construct a new instance based on the current instance. This also means no setter methods!
- Don't directly `unserialize` a value object; this will always create a new instance. If you need out-of-the-box
 serialization functionality, use the SerializableValueObjectTrait and its `serialize` instance method
 and ``fromSerializedValue` factory method.

Enum
----

The Enum namespace contains several useful classes that will help you use Enum objects in your project.
An Enum is a type that has a finite and known set of instances.
You can convert a class to an Enum in two ways:
- By implementing EnumInterface and using the EnumTrait
- By extending the Enum base class, which does this for you

You can then add static factory methods to your Enum to define your instances.

Afterwards, you can use these instance methods or the `::instance` or `instances` method to get the instances.
The EnumTrait guarantees that each instance method always returns the same instance, so you can use
strict equality.

```
class FooBarEnum implements EnumInterface
{
    use EnumTrait;

    public static function FOO(): self
    {
        return self::define('FOO');
    }

    public static function BAR(): self
    {
        return self::define('BAR');
    }
}

FooBarEnum::FOO() === FooBarEnum::FOO() // evaluates to true
```

You can even pass extra initialization data to your define calls and use it to set extra properties for your instances,
by overriding the `initialize` method


```
class FooBarEnum implements EnumInterface
{
    use EnumTrait;

    /** @var string */
    private $message;

    protected function initialize(string $message): void
    {
        $this-message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public static function FOO(): self
    {
        return self::define('FOO', 'foo mama');
    }

    public static function BAR(): self
    {
        return self::define('BAR', 'bar be queue');
    }
}

echo FooBarEnum::FOO()->getMessage(); // outputs 'foo mama'
```
