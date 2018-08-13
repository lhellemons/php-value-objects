Value objects
=============

"Value objects" are objects that are defined by the value of their properties, 
as opposed to "entities", which have an identity that remains the same even if their properties change.
Value objects are considered equal if their properties are equal.

PHP has a number of limitations that make working with value objects difficult to do natively.
Most importantly, there is no suitable native operator to test for value object equality: The equality-by-value operator "=="
does implicit type conversion, which yields false positives, for example when comparing with a variable
that happens to be boolean true. The equality-by-identity operator "===" only yields true if the two operands
are literally the same object instance, which makes sense for entities but not for value objects.

The Value namespace contains several useful classes that will help you use value objects in your project.
As said, a value object is one that is defined solely by its type and the values of its properties.
You can convert a class to a value object in two ways:
- By implementing ValueObjectInterface and using ValueObjectTrait
- By extending the ValueObject base class, which does this for you

You can then add instance properties and static factory methods to your class.
The factory methods should validate and normalize their parameters, and then call `static::fromPropertyValues`
with the parameters. This method can only be used from inside the value object 

Example:
```php
class MyValueType implements ValueObjectInterface
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

$myValueObject = MyValueType::fromFooAndBar('foo', 1);
$myValueObject->getFoo() === 'foo';
$myValueObject->getBar() === 1;

$mySameValueObject = MyValueType::fromFooAndBar('foo', 1);
$myValueObject === $mySameValueObject; // true

$myOtherValueObject = MyValueType::fromFooAndBar('foo', 2);
$myValueObject === $myOtherValueObject; // false

```

Important things to remember:
- Never mutate the value of your instance properties! You can add non-static factory methods that
 use `static::fromValues` to construct a new instance based on the current instance.
 This also means no setter methods! Check out the [money example](examples/money.md).
- Don't directly `unserialize` a value object; this will always create a new instance. If you need out-of-the-box
 serialization functionality, use the SerializableValueObjectTrait and its `serialize` instance method
 and ``fromSerializedValue` factory method.