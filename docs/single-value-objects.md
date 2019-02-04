Single value objects
====================

By far the most common use case for value objects involve an object
represented by a single value, which is also its serialized representation.
For these cases we offer the `SingleValueObjectInterface` and the
`SingleValueObjectTrait`.

A `SingleValueObjectInterface` defines one factory method `of` that
constructs an instance from its serialized representation, one instance
method `getValue` that retrieves the serialized representation, and
also requires that you implement `__toString` to get the string
representation (which is usually the same as the serialized representation).
The `SingleValueObjectTrait` implements these methods for you, so in
most cases you can just define your class like this and it will work:

```php
use SolidPhp\ValueObjects\Value\SingleValueObjectInterface;
use SolidPhp\ValueObjects\Value\SingleValueObjectTrait;

class EmailAddress implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;
}
```

or extend the `SingleValueObject` abstract class, which is provided for
convenience and is basically just a shorthand for the above code block:

```php
use SolidPhp\ValueObjects\Value\SingleValueObject;

class EmailAddress extends SingleValueObject
{
}
```

Validation
----------

In most cases, you won't want to accept just any scalar value. In that
case, you can override the `validateRawValue` method from `SingleValueObjectTrait` and
add any validation you might need:

```php
use SolidPhp\ValueObjects\Value\SingleValueObjectInterface;
use SolidPhp\ValueObjects\Value\SingleValueObjectTrait;

class EmailAddress implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;

    protected static function validateRawValue($rawValue): void
    {
        if (0 === preg_match('/\w+\@\w.com/', $rawValue)) {
            throw new DomainException('Not a valid e-mail address');
        }
    }
}
```

`validateRawValue` should do nothing if the value is valid, and throw
a `DomainException` if the value is not valid in some way.

The argument to `validateRawValue` is the raw value, before it has been
normalized.

Normalization
-------------

You might also want to normalize the value before creating the value
object, for example to trim any whitespace. For this, you can
override the `normalizeValidRawValue` method from `SingleValueObjectTrait`:

```php
use SolidPhp\ValueObjects\Value\SingleValueObjectInterface;
use SolidPhp\ValueObjects\Value\SingleValueObjectTrait;

class EmailAddress implements SingleValueObjectInterface
{
    use SingleValueObjectTrait;

    protected static function normalizeValidRawValue($validRawValue): string 
    {
        return trim($validRawValue);
    }
}
```

`normalizeValidRawValue` is called after validation, so you can assume
the parameter is valid according to the rules of your class.

Considerations
--------------

The same [considerations](value-objects.md#considerations) that apply to any
value object also apply here.

- Don't mutate instance properties
- Don't deserialize directly; always use the `of` factory method
