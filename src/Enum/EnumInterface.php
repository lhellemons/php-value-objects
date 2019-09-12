<?php


namespace SolidPhp\ValueObjects\Enum;

/**
 * Interface EnumInterface
 * @package SolidPhp\ValueObjects\Enum
 *
 * This interface specifies that the implementing class can be used as an enumeration (Enum).
 * Enums define a fixed set of instances that are instantiated by factory methods. The implementing
 * class should guarantee that each factory method will always return the same instance so that
 * strict comparison may be used.
 *
 * The easiest way to implement this interface is to use the EnumTrait.
 *
 * @see EnumTrait
 */
interface EnumInterface
{
    /**
     * Gets an instance of this Enum
     * @param string $id The id of the instance to retrieve
     * @param bool $throwIfNotFound If true, and there is no instance for the given $id, a DomainException will be
     * thrown. If false (default), null will be returned
     * @return static|null
     * @throws \DomainException if $throwIfNotFound is true and there is no instance for the given $id
     */
    public static function instance(string $id, bool $throwIfNotFound = false);

    /**
     * Returns an array containing all instances of this Enum
     * @return static[]
     */
    public static function instances(): array;

    /**
     * Gets the id of this Enum instance
     * @return string
     */
    public function getId(): string;
}
