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

The Value namespace contains the `ValueObjectTrait`, which will help you use value objects in your project.
The main advantage is that the same combination of constructor parameters will yield the same instance,
so that it becomes possible to use strict comparison (`===`) to compare value objects.

For most cases, you will want a value object represented by a single value. For those cases,
you can use the `SingleValueObject` types. Read about them [here](single-value-objects.md)

If you want the ability to take in any number of values, you can designate a class as a value object as follows:
- Add the ValueObjectTrait to the class
- Make the class constructor private (or protected, if you want to support subclasses).
- Add one or more public static factory methods to the class. The factory methods should validate and normalize
their parameters, and then call `return static::getInstance()` with the parameters needed by the constructor.

Example:
```php
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

class Point
{
    // use ValueObjectTrait to get access to the getInstance class method to use in your factory methods
    use ValueObjectTrait;

    // properties work just as normal
    /** @var int */
    private $x;

    /** @var int */
    private $y;

    // the constructor should be private or protected to prevent it being used from outside the factory methods
    protected function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    // add a static factory method to create new instances by calling the getInstance method
    public static function fromCoordinates(int $x, int $y): self
    {
        // getInstance accepts the same parameters as the constructor, in the same order.
        // treat the call as if you were calling "new self()"
        return self::getInstance($x, $y);
    }

    // of course, factory methods are free to use whatever logic to translate / normalize their parameters
    // into the proper arguments to getInstance.
    public static function closestTo(float $x, float $y): self
    {
        return self::getInstance(round($x), round($y));
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }
}

$myPoint = Point::fromCoordinates(1, 2);
$myPoint->getX() === 1;
$myPoint->getY() === 2;

$mySamePoint = Point::fromCoordinates(1, 2);
$myPoint === $mySamePoint; // true

$myOtherPoint = Point::fromCoordinates(1, 3);
$myPoint === $myOtherPoint; // false
```

<a name="considerations"></a>
Important things to remember
----------------------------

Value objects add many advantages to your codebase. These flow directly
from the following usage rules:

- Never mutate the value of your instance properties! You can add non-static factory methods that
  use `static::getInstance` to construct a new instance based on the current instance.
  This also means no setter methods! Check out the [money example](examples/money.md) for an example of how to work with immutable
  value object instances.
- Value objects cannot directly be `unserialize`d; this would always create a new instance. Value objects
  usually contain only scalars as properties, so serialization / unserialization should be a matter
  of getting those scalars for serialization and using the unserialized scalars to produce the correct
  call to a factory method for deserialization. In the future this library may add a trait that will
  automate this.
- Although it is allowed for value objects to take mutable objects (entities) as properties, this should be done
  with some caution. The 'value' of value objects with mutable properties will not change if the mutable property
  is changed. In most cases this matches expected behavior, as illustrated below:

```php
$entity = new Entity();
$entity->property = 1;
$valueObjectB = ValueClass::fromEntity($entity);
$entity->property = 2;
$valueObjectB = ValueClass::fromEntity($entity);
$valueObjectA === $valueObjectB; // true
```

WeakRef
-------

Value objects support the PHP [WeakRef extension](http://php.net/manual/en/book.weakref.php). 
If your PHP runtime has the WeakRef extension installed, value object
 instances will be kept as weak references, and will be 
 garbage-collected
 when they are no longer referenced in code. This means it becomes
 possible to instantiate enormous numbers of value objects for
 quick tasks without consuming excessive memory.

If the WeakRef extension is not installed, value object instances will
be kept in memory until the script finishes.
