<?php


namespace SolidPhp\ValueObjects\Enum;

abstract class Enum implements EnumInterface
{
    use EnumTrait;

    /** @var string */
    private $id;

    /**
     * Default constructor. Stores the id in the instance for easy lookup, ignores all other
     * arguments.
     * Override the constructor in your subclass to change this behavior.
     *
     * @param string $id
     * @param array  $arguments
     */
    protected function __construct(string $id, ...$arguments)
    {
        $this->id = $id;
    }

    final public function getId(): string
    {
        return $this->id;
    }

    public function __toString()
    {
        return sprintf('%s::%s', static::class,$this->id);
    }
}
