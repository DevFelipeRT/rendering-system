<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context\Dto;

use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextInterface;

/**
 * Provides a base implementation for context Data Transfer Objects.
 *
 * This abstract class implements the generic, collection-like methods defined in
 * the ContextInterface, operating on an internal data array that must be provided
 * by the concrete child class.
 */
abstract class AbstractContext implements ContextInterface
{
    /**
     * The underlying data store for the context.
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * {@inheritdoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }
}