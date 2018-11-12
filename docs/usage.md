Usage
-----

First, add the package to your composer dependencies

```
composer require lhellemons/php-value-objects
```

Then, use the classes or traits in your own designs.

### Enums

```php
<?php

use SolidPhp\ValueObjects\Enum\EnumTrait;

final class MyEnum
{
    use EnumTrait;

    public static function FOO(): self
    {
        return self::define('FOO');
    }
}
```

Check out the specific documentation for Enums here: [Enums documentation](enums.md)

### Value objects

```php

use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class MyValueObject
{
    use ValueObjectTrait;

    /** @var string */
    private $slug;

    private function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function fromSlug(string $slug): self
    {
        return static::getInstance(strtolower(trim($slug)));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
```

Check out the specific documentation for value objects here: [Value objects documentation](value-objects.md)
