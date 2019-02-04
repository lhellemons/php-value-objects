Enum
====

The Enum namespace contains several useful classes that will help you use Enum objects in your project.
An Enum is a type that has a finite and known set of instances.
You can convert a class to an Enum in two ways:
- By implementing EnumInterface and using the EnumTrait
- By extending the Enum base class, which does this for you

You can then add static factory methods to your Enum to define your instances.

Afterwards, you can use these factory methods or the `::instance` or `::instances` methods to get the instances.
The EnumTrait guarantees that each factory method always returns the same instance, so you can use
strict equality (===) comparison.

```php
use SolidPhp\ValueObjects\Enum\EnumInterface;
use SolidPhp\ValueObjects\Enum\EnumTrait;

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

FooBarEnum::FOO() === FooBarEnum::FOO() // true
```

If you want to store extra data on your Enum instances, you can add a
constructor function. The constructor will be passed the id and any
extra arguments passed to the `define` calls by your factory methods;
you can use them just as in normal classes.

```php
use SolidPhp\ValueObjects\Enum\EnumInterface;
use SolidPhp\ValueObjects\Enum\EnumTrait;

class FooBarEnum implements EnumInterface
{
    use EnumTrait;

    /** @var string */
    private $message;

    protected function __construct(string $id, string $message)
    {
        $this->message = $message;
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
        return self::define('BAR', 'bar be queued');
    }
}

echo FooBarEnum::FOO()->getMessage(); // outputs 'foo mama'
```

Enum classes can have subclasses as well, but the following caveats apply:
- although the factory methods are inherited from the parent Enum, these
 still produce instances of the class on which they are called, so
 `Parent::FOO()` _will not be equal to_ `Child::FOO()`.
- If the parent class defines factory methods that are not overridden by
 the child class, and the child has its own constructor, the constructor
  must accept _at least the same parameters_ as the parent constructor,
  and any extra parameters not accepted by the parent constructor _must
  be optional_, so that the data from the parent factory methods can be
  accepted by the child constructor.
