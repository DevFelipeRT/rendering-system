<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Shared\CollectionInterface;

/**
 * Defines the contract for a collection of partial view components.
 *
 * This interface ensures that all implementations provide a type-safe,
 * immutable collection of PartialViewInterface objects indexed by
 * string identifiers.
 */
interface PartialsCollectionInterface extends CollectionInterface
{
}
