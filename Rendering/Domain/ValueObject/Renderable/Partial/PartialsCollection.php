<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\ValueObject\Shared\Collection;
use Rendering\Domain\Trait\Validation\PartialsValidationTrait;

/**
 * Type-safe, immutable collection of partial view components.
 *
 * This Value Object extends the base Collection class and implements
 * PartialsCollectionInterface to provide specialized operations for
 * PartialViewInterface objects. It ensures type safety and immutability
 * while offering convenient methods for partial management.
 *
 * Key Features:
 * - Type-safe storage of PartialViewInterface objects only
 * - Immutable operations inherited from base Collection
 * - Specialized methods for partial filtering and validation
 * - Integration with validation traits for data integrity
 *
 * @extends Collection<string, PartialViewInterface>
 */
final class PartialsCollection extends Collection implements PartialsCollectionInterface
{
    use PartialsValidationTrait;

    /**
     * Constructs a new PartialsCollection instance.
     *
     * @param array<string, PartialViewInterface> $partials An associative array of partials.
     * @throws InvalidArgumentException if the partials array is invalid.
     */
    public function __construct(array $partials)
    {
        $this->validatePartials($partials);
        parent::__construct($partials);
    }

    /**
     * Retrieves a partial by its unique identifier.
     *
     * @param string $identifier The identifier of the partial to retrieve.
     * @return PartialViewInterface
     * @throws InvalidArgumentException if no partial with the given identifier exists.
     */
    public function getPartial(string $identifier): PartialViewInterface
    {
        $partial = $this->get($identifier);
        
        if (!$partial instanceof PartialViewInterface) {
            throw new InvalidArgumentException("Item with identifier '{$identifier}' is not a PartialViewInterface.");
        }
        
        return $partial;
    }

    /**
     * Adds a new partial to the collection.
     *
     * @param string $identifier The unique identifier for the partial.
     * @param PartialViewInterface $partial The partial to add.
     * @return self A new collection instance with the partial added.
     */
    public function addPartial(string $identifier, PartialViewInterface $partial): self
    {
        return new self($this->set($identifier, $partial)->all());
    }

    /**
     * Removes a partial from the collection.
     *
     * @param string $identifier The identifier of the partial to remove.
     * @return self A new collection instance with the partial removed.
     */
    public function removePartial(string $identifier): self
    {
        return new self($this->remove($identifier)->all());
    }

    /**
     * Filters partials by template file pattern.
     *
     * @param string $pattern The pattern to match against template filenames.
     * @return self A new collection containing only matching partials.
     */
    public function filterByTemplate(string $pattern): self
    {
        $filtered = $this->filter(function (PartialViewInterface $partial) use ($pattern): bool {
            return str_contains($partial->fileName(), $pattern);
        });

        return new self($filtered->all());
    }
}
