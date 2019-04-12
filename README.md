PHP Value Objects SDK
=====================

[![Build Status](https://travis-ci.org/lhellemons/php-value-objects.svg?branch=master)](https://travis-ci.org/lhellemons/php-value-objects)

Author: Laurens Hellemons <lhellemons@gmail.com>

This library contains utility classes, traits and interfaces for working
 with value objects in PHP. By using these, you can easily define your
 own value objects.

Read the full documentation [here](docs/index.md).

Why value objects?
------------------
Value objects are a core component of domain-driven design (DDD). They are
objects that do not have an identity, and are instead defined by their values.
Any large system that deals with domain concepts will eventually develop
a need to model some of these concepts as value objects.
Examples of such concepts include

- Dates
- Amounts of money
- Coordinates
- Adresses
- Physical quantities such as age, length or weight

This library helps circumvent PHP's limited support for value objects.
Using the classes, interfaces and traits provided by this package, you can 
create your own value objects without worrying about the internals.

Usage
-----

Install the package using composer.

```bash
composer require lhellemons/php-value-objects
```

Then, use the classes or traits in your own designs.

```php
<?php

use SolidPhp\ValueObjects\Enum\EnumTrait;

final class Weekday
{
    use EnumTrait;

    public static function MONDAY(): self
    {
        return self::define('MONDAY');
    }

    public static function TUESDAY(): self
    {
        return self::define('TUESDAY');
    }

    // ...
}

// ...

$monday = Weekday::MONDAY();
$tuesday = Weekday::TUESDAY();
$deliveryDay = WeekDay::MONDAY();

$monday === $deliveryDay; // true
$monday === $tuesday; // false
```

```php

use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class EmailAddress
{
    use ValueObjectTrait;

    /** @var string */
    private $emailAddressString;

    private function __construct(string $emailAddressString)
    {
        $this->emailAddressString = $emailAddressString;
    }

    public function of(string $emailAddressString): self
    {
        $normalizedString = strtolower(trim($emailAddressString));
        return static::getInstance($normalizedString);
    }

    public function getString(): string
    {
        return $this->emailAddressString;
    }
}

// ...

$emailAddress = EmailAddress::of("annie@email.com");
$sameEmailAddress = EmailAddress::of(" ANNIE@EMAIL.COM");

$emailAddress === $sameEmailAddress; // true
```
