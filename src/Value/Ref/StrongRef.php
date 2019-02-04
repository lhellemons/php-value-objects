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

    /**
     * @return object|null
     */
    public function get()
    {
        return $this->object;
    }

    /**
     * @param object|null $object
     */
    public function set($object): void
    {
        $this->object = $object;
    }

    public function has(): bool
    {
        return $this->object !== null;
    }
}
