<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Context;

/**
 * Defines a base contract for Data Transfer Objects used in rendering.
 *
 * This interface provides generic, collection-like methods for accessing the
 * underlying data of a context object in a consistent way.
 */
interface ContextInterface
{
    /**
     * Retrieves a specific value from the context by its key.
     *
     * @param string $key The key of the data to retrieve.
     * @param mixed|null $default A default value to return if the key is not found.
     * @return mixed The value associated with the key, or the default value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Checks if a specific key exists in the context.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key): bool;

    /**
     * Retrieves all data from the context as an associative array.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Checks if the context contains any data.
     *
     * @return bool True if the context is empty, false otherwise.
     */
    public function isEmpty(): bool;
}