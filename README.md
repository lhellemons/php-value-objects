SolidPHP Value Objects library
==============================

Author: Laurens Hellemons <lhellemons@gmail.com>

This library contains utility classes, traits and interfaces for working with value objects in PHP.
By using these, you can easily define your own value objects.

Read the full documentation [here](docs/index.md).

Usage
-----

```
composer require lhellemons/php-value-objects
```

Then, use the classes or traits in your own designs.

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
