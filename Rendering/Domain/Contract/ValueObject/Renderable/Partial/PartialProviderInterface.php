<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Contract for objects capable of providing partial renderable components.
 *
 * This interface establishes a contract for implementing a compositional rendering
 * architecture where entities can expose collections of partial templates or views.
 * It enables both primary pages and individual partial views to serve as contexts
 * for rendering their nested sub-components, promoting modularity and reusability
 * in template composition.
 *
 * By extending RenderableInterface, this contract ensures that all partial providers
 * are inherently renderable objects themselves, establishing a consistent interface
 * for all components in the rendering hierarchy.
 *
 * Implementing classes should provide access to their associated partial components
 * through a structured collection, facilitating hierarchical rendering patterns
 * and supporting complex UI compositions.
 */
interface PartialProviderInterface extends RenderableInterface
{
    /**
     * Retrieves the collection of partial components associated with this provider.
     *
     * Returns a collection containing all partial templates or views that should
     * be rendered as part of this provider's context. The collection may be null
     * if no partials are associated with the current provider instance.
     *
     * @return PartialsCollectionInterface|null Collection of partial components, or null if none exist
     */
    public function partials(): ?PartialsCollectionInterface;
}
