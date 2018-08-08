<?php

namespace SolidPhp\ValueObjects\Value;

class ValueObject implements ValueObjectInterface
{
    use ValueObjectTrait;

    public function __toString()
    {
        return sprintf(
            '%s(%s)',
            static::class,
            implode(
                ', ',
                array_map(
                    function ($value) {
                        return json_encode($value);
                    },
                    $this->getValues()
                )
            )
        );
    }
}
