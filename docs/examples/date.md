Date example
============

Working with dates without a time component is a common task in many
PHP applications. PHP provides a `DateTime` class (and an immutable variant called `DateTimeImmutable`) for working with datetimes, which
model a specific moment in time, but there is no built-in way of
dealing with only dates; if you want to, say, compare dates, you'll
have to be careful to first remove or equalize the time and timezone components of both datetimes.

```php
$dateA = DateTime::createFromFormat('Y-m-d', '2019-01-01');
// one second later
$dateB = DateTime::createFromFormat('Y-m-d', '2019-01-01');

$dateA == $dateB; // true or false, depending on if the assignments occurred on the same system second!!!
```

With the power of value objects, we have the capability of
defining a value object class that encapsulates the concept of a Date,
and provides useful methods for construction and comparison.

```php
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Date
{
    use ValueObjectTrait;

    /** @var DateTimeImmutable */
    private $dateTime;

    private function __construct(int $year, int $month, int $day) {
        // we can make sure the internal dateTimes are always the
        // start of day of the given source DateTimeInterface in the
        // GMT timezone.
        $this->dateTime = DateTimeImmutable::createFromFormat(DateTime::ATOM, sprintf('%d-%d-%dT00:00:00+00:00', $year, $month, $day));
    }

    public static function of($source): self
    {
        if ($source instanceof DateTimeInterface) {
            $matches = [];
            if (1 === preg_match('/^(\\d{4})-(\\d{2})-(\\d{2})$/', $source->format('Y-m-d'), $matches)) {
                [, $year, $month, $day] = $matches;

                return self::getInstance((int)$year, (int)$month, (int)$day);
            }
        }

        if (is_string($source)) {
            return self::of(new DateTimeImmutable($source));
        }
    }

    // we can add factory methods that create Dates based on other Dates
    public function add(DateInterval $interval): self
    {
        return self::of($this->dateTime->add($interval));
    }

    // we can use comparison operators on the internal datetime
    public function compare(Date $date): int
    {
        return $this->dateTime <=> $date->dateTime;
    }

    public function isBefore(Date $date): bool
    {
        return $this->compare($date) < 0;
    }

    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }
}

$date = Date::of('2019-01-01');
$sameDate = Date::of('2019-01-01');
$otherDate = Date::of('2019-01-02');

$date === $sameDate; // true
$date === $otherDate; // false
$date < $otherDate; // true -- this works because PHP compares the internal properties (i.e. the `DateTime`s)

$nextDate = $date->add(new DateInterval('P1D'));
$nextDate === $otherDate; // true

```
