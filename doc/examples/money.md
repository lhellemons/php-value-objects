The Money pattern
=================

One of the most common value object patterns is the Money pattern, described by
Martin Fowler (TODO add reference). Here is a sample implementation using value objects:

```php
class Currency implements ValueObjectInterface
{
    use ValueObjectTrait;
    
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

class Money implements ValueObjectInterface
{
    use ValueObjectTrait;
    
    /** @var int */
    private $amount;
    
    /** @var Currency */
    private $currency;
    
    public static function zero(Currency $currency): self
    {
        return self::fromPropertyValues(['amount' => 0, 'currency' => $currency]);
    }
    
    public static function amount(int $amount, Currency $currency): self
    {
        return self::fromPropertyValues(['amount' => $amount, 'currency' => $currency]);
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
        return self::amount(round($this->amount * $multiplier), $this->currency);
    }
    
    public function allocate(int $count): array
    {
        // TODO allocate algorithm
    }
    
    private function assertSameCurrency(Money $money): void
    {
        if ($money->currency !== $this->currency) {
            throw new DomainException(
                sprintf(
                    'Trying to operate on Money objects of different currencies %s and %s',
                    $this->currency,
                    $money->currency
                )
            );
        }
    }
}

```

This class could be used as such:

```php
$usd = Currency::USD();

$price1 = Money::amount(1000, $usd);
$price2 = Money::amount(500, $usd);
$totalPrice = $price1->add($price2);
$totalPriceWithVat = $price->add($totalPrice->multiply(0.2));
$nrOfTerms = 2;
$termPrices = $price->allocate($nrOfTerms);

// $termPrices == [Money::amount(900, $usd), Monay::amount(900, $usd)] 
```