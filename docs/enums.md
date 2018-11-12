Enum
====

The Enum namespace contains several useful classes that will help you use Enum objects in your project.
An Enum is a type that has a finite and known set of instances.
You can convert a class to an Enum in two ways:
- By implementing EnumInterface and using the EnumTrait
- By extending the Enum base class, which does this for you

You can then add static factory methods to your Enum to define your instances.

Afterwards, you can use these instance methods or the `::instance` or `instances` method to get the instances.
The EnumTrait guarantees that each instance method always returns the same instance, so you can use
strict equality.

```php
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


```php
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
