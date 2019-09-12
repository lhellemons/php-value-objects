<?php

namespace SolidPhp\ValueObjects\Value\Ref;

/**
 * Class StrongRef
 * @internal
 */
class StrongRef extends Ref
{
    /** @var object|null */
    private $object;

    public function get()
    {
        return $this->object;
    }

    public function set($object): void
    {
        /** @psalm-suppress MixedAssignment */
        $this->object = $object;
    }

    public function has(): bool
    {
        return $this->object !== null;
    }
}
