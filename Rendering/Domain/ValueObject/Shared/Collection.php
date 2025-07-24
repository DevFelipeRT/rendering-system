<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Shared;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Rendering\Domain\Contract\ValueObject\Shared\CollectionInterface;

/**
 * Base implementation of a type-safe, immutable collection Value Object.
 *
 * This class provides a concrete implementation of CollectionInterface with
 * full CRUD operations, functional programming methods, and immutable behavior.
 * It serves as the foundation for specialized collection implementations.
 *
 * Key Features:
 * - Immutable operations that return new collection instances
 * - Type-safe storage with mixed value support
 * - Iterator and Countable interface implementation
 * - Comprehensive set of collection manipulation methods
 * - Validation and error handling
 *
 * @template TKey of array-key
 * @template TValue
 * @implements CollectionInterface<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 */
class Collection implements CollectionInterface, IteratorAggregate, Countable
{
    /**
     * @var array<array-key, mixed> Internal storage for collection items
     */
    private readonly array $items;

    /**
     * Creates a new collection instance.
     *
     * @param array<array-key, mixed> $items Initial items for the collection
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string|int $key): mixed
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException("Key '{$key}' does not exist in the collection.");
        }

        return $this->items[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string|int $key, mixed $value): self
    {
        $newItems = $this->items;
        $newItems[$key] = $value;

        return new self($newItems);
    }

    /**
     * {@inheritdoc}
     */
    public function add(mixed $value): self
    {
        $newItems = $this->items;
        $newItems[] = $value;

        return new self($newItems);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string|int $key): self
    {
        if (!$this->has($key)) {
            return $this;
        }

        $newItems = $this->items;
        unset($newItems[$key]);

        return new self($newItems);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function values(): array
    {
        return array_values($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function first(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        return reset($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function last(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        return end($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $callback): self
    {
        $filtered = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);

        return new self($filtered);
    }

    /**
     * {@inheritdoc}
     */
    public function map(callable $callback): self
    {
        $mapped = array_map($callback, $this->items);

        return new self($mapped);
    }

    /**
     * {@inheritdoc}
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * {@inheritdoc}
     */
    public function find(callable $callback): mixed
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(callable $callback): bool
    {
        return $this->find($callback) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(CollectionInterface $other): self
    {
        $merged = array_merge($this->items, $other->all());

        return new self($merged);
    }

    /**
     * {@inheritdoc}
     */
    public function with(string|int $key, mixed $value): self
    {
        return $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function without(string|int $key): self
    {
        return $this->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function slice(int $offset, ?int $length = null): self
    {
        $sliced = array_slice($this->items, $offset, $length, true);

        return new self($sliced);
    }

    /**
     * {@inheritdoc}
     */
    public function chunk(int $size): self
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Chunk size must be greater than 0.');
        }

        $chunks = array_chunk($this->items, $size, true);

        return new self($chunks);
    }

    /**
     * {@inheritdoc}
     */
    public function sort(callable $callback): self
    {
        $sorted = $this->items;
        uasort($sorted, $callback);

        return new self($sorted);
    }

    /**
     * {@inheritdoc}
     */
    public function reverse(): self
    {
        $reversed = array_reverse($this->items, true);

        return new self($reversed);
    }

    /**
     * {@inheritdoc}
     */
    public function unique(): self
    {
        $unique = array_unique($this->items, SORT_REGULAR);

        return new self($unique);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Creates a new collection from an array.
     *
     * @param array<array-key, mixed> $items
     * @return self
     */
    public static function from(array $items): self
    {
        return new self($items);
    }

    /**
     * Creates an empty collection.
     *
     * @return self
     */
    public static function empty(): self
    {
        return new self([]);
    }
}
