<?php

namespace SolidPhp\ValueObjects\Value\Ref;

use \WeakRef as PhpWeakRef;

/**
 * Class WeakRef
 * A Ref implementation that uses the PECL WeakRef extension (available in PHP < 7.3)
 * See https://www.php.net/manual/en/book.weakref.php
 * @internal
 */
class PeclWeakRef extends Ref
{
    /** @var PhpWeakRef|null */
    private $weakRef;

    public function get()
    {
        return $this->weakRef ? $this->weakRef->get() : null;
    }

    public function set($object): void
    {
        $this->weakRef = new PhpWeakRef($object);
    }

    public function has(): bool
    {
        return $this->weakRef && $this->weakRef->valid();
    }
}
