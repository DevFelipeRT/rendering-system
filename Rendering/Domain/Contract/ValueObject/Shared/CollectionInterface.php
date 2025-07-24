<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Shared;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Defines the base contract for all immutable collection value objects.
 *
 * This interface provides a common set of operations for type-safe,
 * immutable collections indexed by string identifiers. All specific
 * collection interfaces should extend this base contract.
 *
 * @template T The type of objects stored in the collection
 */
interface CollectionInterface extends IteratorAggregate, Countable
{
    /**
     * Retrieves an item by its unique identifier.
     *
     * @param string|int $identifier The identifier of the item to retrieve.
     * @return mixed The item with the given identifier.
     * @throws InvalidArgumentException if no item with the given identifier exists.
     */
    public function get(string|int $identifier): mixed;

    /**
     * Checks if an item with the given identifier exists in the collection.
     *
     * @param string $identifier The identifier to check.
     * @return bool True if the item exists, false otherwise.
     */
    public function has(string $identifier): bool;

    /**
     * Returns the entire collection as an associative array.
     *
     * @return array<string, mixed> The complete collection as an array.
     */
    public function all(): array;

    /**
     * Returns the number of items in the collection.
     *
     * @return int The count of items in the collection.
     */
    public function count(): int;

    /**
     * Allows the collection to be iterated over using foreach.
     *
     * @return ArrayIterator<string, mixed> Iterator for the collection.
     */
    public function getIterator(): ArrayIterator;

    /**
     * Converts the collection to an associative array.
     *
     * @return array<string, mixed> The collection as an associative array.
     */
    public function toArray(): array;

    /**
     * Checks if the collection is empty.
     *
     * @return bool True if the collection contains no items, false otherwise.
     */
    public function isEmpty(): bool;

    /**
     * Gets the keys (identifiers) of all items in the collection.
     *
     * @return array<string> Array of item identifiers.
     */
    public function keys(): array;

    /**
     * Creates a new collection with an additional item.
     *
     * This method maintains immutability by returning a new collection instance
     * rather than modifying the current one.
     *
     * @param string $identifier The identifier for the new item.
     * @param mixed $item The item to add to the collection.
     * @return self A new collection instance with the added item.
     */
    public function with(string $identifier, mixed $item): self;

    /**
     * Creates a new collection without the specified item.
     *
     * This method maintains immutability by returning a new collection instance
     * rather than modifying the current one.
     *
     * @param string $identifier The identifier of the item to remove.
     * @return self A new collection instance without the specified item.
     */
    public function without(string $identifier): self;

    /**
     * Creates a new collection by merging with another collection.
     *
     * Items from the other collection will override items with the same
     * identifier in the current collection.
     *
     * @param self $other The collection to merge with.
     * @return self A new collection instance containing items from both collections.
     */
    public function merge(self $other): self;

    /**
     * Creates a new collection containing only items that satisfy the given predicate.
     *
     * @param callable $predicate A function that takes an item and returns bool.
     * @return self A new filtered collection instance.
     */
    public function filter(callable $predicate): self;

    /**
     * Gets the first item in the collection.
     *
     * @return mixed|null The first item or null if collection is empty.
     */
    public function first(): mixed;

    /**
     * Gets the last item in the collection.
     *
     * @return mixed|null The last item or null if collection is empty.
     */
    public function last(): mixed;
}
