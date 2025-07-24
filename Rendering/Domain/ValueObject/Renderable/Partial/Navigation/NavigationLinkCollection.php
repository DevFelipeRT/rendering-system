<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial\Navigation;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkCollectionInterface;
use Rendering\Domain\ValueObject\Shared\Collection;
use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkInterface;
use Rendering\Domain\Trait\Validation\NavigationLinkValidationTrait;

/**
 * Type-safe, immutable collection of navigation link components.
 *
 * This Value Object extends the base Collection class and implements
 * NavigationLinkCollectionInterface to provide specialized operations for
 * NavigationLinkInterface objects. It ensures type safety and immutability
 * while offering convenient methods for navigation link management.
 *
 * Key Features:
 * - Type-safe storage of NavigationLinkInterface objects only
 * - Immutable operations inherited from base Collection
 * - Specialized methods for navigation link filtering
 * - Integration with validation traits for data integrity
 *
 * @extends Collection<string, NavigationLinkInterface>
 */
final class NavigationLinkCollection extends Collection implements NavigationLinkCollectionInterface
{
    use NavigationLinkValidationTrait;

    /**
     * Constructs a new NavigationLinkCollection instance.
     *
     * @param array<string, NavigationLinkInterface> $links An associative array of navigation links.
     * @throws InvalidArgumentException if the links array contains invalid items.
     */
    public function __construct(array $links)
    {
        $this->validateLinkArray(array_values($links));
        parent::__construct($links);
    }

    /**
     * Retrieves a navigation link by its unique identifier.
     *
     * @param string|int $identifier The identifier of the link to retrieve.
     * @return NavigationLinkInterface
     * @throws InvalidArgumentException if no link with the given identifier exists.
     */
    public function get(string|int $identifier): NavigationLinkInterface
    {
        $link = $this->get($identifier);
        
        if (!$link instanceof NavigationLinkInterface) {
            throw new InvalidArgumentException("Item with identifier '{$identifier}' is not a NavigationLinkInterface.");
        }
        
        return $link;
    }

    /**
     * Adds a new navigation link to the collection.
     *
     * @param string $identifier The unique identifier for the link.
     * @param NavigationLinkInterface $link The navigation link to add.
     * @return self A new collection instance with the link added.
     */
    public function addLink(string $identifier, NavigationLinkInterface $link): self
    {
        return new self($this->set($identifier, $link)->all());
    }

    /**
     * Removes a navigation link from the collection.
     *
     * @param string $identifier The identifier of the link to remove.
     * @return self A new collection instance with the link removed.
     */
    public function removeLink(string $identifier): self
    {
        return new self($this->remove($identifier)->all());
    }

    /**
     * Filters the collection by active status.
     *
     * @param bool $active Whether to return active or inactive links.
     * @return self A new collection containing only links with the specified active status.
     */
    public function filterByActive(bool $active = true): self
    {
        $filtered = $this->filter(function (NavigationLinkInterface $link) use ($active): bool {
            return $link->active() === $active;
        });

        return new self($filtered->all());
    }

    /**
     * Filters links by URL pattern.
     *
     * @param string $pattern The pattern to match against link URLs.
     * @return self A new collection containing only matching links.
     */
    public function filterByUrl(string $pattern): self
    {
        $filtered = $this->filter(function (NavigationLinkInterface $link) use ($pattern): bool {
            return str_contains($link->url(), $pattern);
        });

        return new self($filtered->all());
    }
}
