The Money pattern
=================

One of the most common value object patterns is the Money pattern, as described by
Martin Fowler (https://martinfowler.com/eaaCatalog/money.html).
Here is a sample implementation using a ValueObject:

```php
use SolidPhp\ValueObjects\Enum\EnumInterface;
use SolidPhp\ValueObjects\Enum\EnumTrait;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

class Currency implements EnumInterface
{
    use EnumTrait;
    
    public static function EUR(): self
    {
        return self::define('EUR');
    }
    
    public static function USD(): self
    {
        return self::define('USD');
    }
    
    public static function GBP(): self
    {
        return self::define('GBP');
    }
}

final class Money
{
    use ValueObjectTrait;
    
    /** @var int */
    private $amount;
    
    /** @var Currency */
    private $currency;

    private function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }
    
    public static function zero(Currency $currency): self
    {
        return self::getInstance(0, $currency);
    }
    
    public static function amount(int $amount, Currency $currency): self
    {
        return self::getInstance($amount, $currency);
    }
    
    public function add(Money $money): self
    {
        $this->assertSameCurrency($money);
        
        return self::amount($this->amount + $money->amount, $this->currency);
    }
    
    public function subtract(Money $money): self
    {
        $this->assertSameCurrency($money);
        
        return self::amount($this->amount - $money->amount, $this->currency);
    }
    
    public function multiply(float $multiplier): self
    {
        return self::amount(round($this->amount * $multiplier, 0), $this->currency);
    }

    /**
     * @param int $count The number of places to allocate the money to. Must be greater than 0.
     * @return Money[]   An array of $count Money objects that sum to the original money
     */
    public function allocate(int $count): array
    {
        if ($count <= 0) {
            throw new DomainException('Cannot allocate to 0 or fewer places');
        }

        $share = $this->multiply(1 / $count);
        $shares = array_fill(0, $count, $share);
        $remainder = $this->subtract($share->multiply($count));
        $shares[$count-1] = $shares[$count-1]->add($remainder);

        return $shares;
    }
    
    private function assertSameCurrency(Money $money): void
    {
        if ($money->currency !== $this->currency) {
            throw new DomainException(
                \sprintf(
                    'Trying to operate on Money objects of different currencies %s and %s',
                    $this->currency,
                    $money->currency
                )
            );
        }
    }
}

```

This class could be used as follows:

```php
$usd = Currency::USD();

$price1 = Money::amount(1000, $usd);
$price2 = Money::amount(500, $usd);
$totalPrice = $price1->add($price2);
$totalPriceWithVat = $price->add($totalPrice->multiply(0.2));
$nrOfTerms = 2;
$termPrices = $totalPrice->allocate($nrOfTerms);

// $termPrices == [Money::amount(750, $usd), Monay::amount(750, $usd)]
```
