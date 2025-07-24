<?php

declare(strict_types=1);

namespace Rendering\Domain\Trait\ValueObject\Renderable;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;

/**
 * Trait for providing partial renderable components functionality.
 *
 * This trait provides a default implementation of PartialProviderInterface,
 * offering reusable functionality for Value Objects that need to manage and expose
 * collections of partial templates or views. It handles the storage and
 * retrieval of partials while maintaining immutability principles.
 *
 * Key Features:
 * - Immutable storage of partials collection
 * - Complete implementation of PartialProviderInterface methods
 * - Null-safe partial collection handling
 * - Can be used with any class that needs partial provider functionality
 *
 * Classes using this trait gain the ability to:
 * - Store and expose partial components
 * - Participate in hierarchical rendering patterns
 * - Support complex UI compositions through partial injection
 * - Mix partial provider functionality with other inheritance hierarchies
 *
 * Usage:
 * ```php
 * class MyComponent implements PartialProviderInterface
 * {
 *     use PartialProviderTrait;
 *
 *     public function __construct(?PartialsCollectionInterface $partials = null)
 *     {
 *         $this->initializePartials($partials);
 *     }
 * }
 * ```
 */
trait PartialProviderTrait
{
    /**
     * The collection of partial components associated with this provider.
     *
     * @var PartialsCollectionInterface|null
     */
    protected ?PartialsCollectionInterface $partialsCollection = null;

    /**
     * Initializes the partials collection.
     *
     * This method should be called from the constructor of classes using this trait
     * to properly initialize the partials collection.
     *
     * @param PartialsCollectionInterface|null $partials The collection of partial components, or null if none
     */
    protected function initializePartials(?PartialsCollectionInterface $partials = null): void
    {
        $this->partialsCollection = $partials;
    }

    /**
     * {@inheritdoc}
     */
    public function partials(): ?PartialsCollectionInterface
    {
        return $this->partialsCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPartials(): bool
    {
        return $this->partialsCollection !== null && !$this->partialsCollection->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialsCount(): int
    {
        return $this->partialsCollection?->count() ?? 0;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPartial(string $identifier): bool
    {
        return $this->partialsCollection?->has($identifier) ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPartial(string $identifier): mixed
    {
        if (!$this->hasPartial($identifier)) {
            return null;
        }

        return $this->partialsCollection->get($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialIdentifiers(): array
    {
        if ($this->partialsCollection === null) {
            return [];
        }

        return $this->partialsCollection->keys();
    }
}
