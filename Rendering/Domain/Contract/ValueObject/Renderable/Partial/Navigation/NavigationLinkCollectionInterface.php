<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation;

use Rendering\Domain\Contract\ValueObject\Shared\CollectionInterface;

/**
 * Defines the contract for a collection of navigation link components.
 *
 * This interface ensures that all implementations provide a type-safe,
 * immutable collection of NavigationLink objects indexed by
 * string identifiers for navigation menu management.
 */
interface NavigationLinkCollectionInterface extends CollectionInterface
{
    /**
     * Filters the collection by active status.
     *
     * @param bool $active Whether to return active or inactive links.
     * @return self A new collection containing only links with the specified active status.
     */
    public function filterByActive(bool $active = true): self;
}
