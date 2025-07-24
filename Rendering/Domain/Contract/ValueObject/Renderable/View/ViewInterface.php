<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\View;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialProviderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a primary View Value Object.
 *
 * A View represents the main content component for a specific application route
 * or page. It serves as the central content unit that is rendered within layouts
 * and can provide access to related partial components.
 *
 * Key Responsibilities:
 * - Encapsulate the main renderable content with template path and data
 * - Provide SEO-related metadata (title, description) for the view
 * - Act as a provider for related partial components when needed
 * - Maintain type safety distinction from PartialViewInterface objects
 *
 * This interface ensures that main view rendering methods can only accept
 * appropriate view objects, preventing confusion with partial components
 * and maintaining clear architectural boundaries.
 *
 * @extends RenderableInterface Provides core rendering capabilities
 * @extends PartialProviderInterface Allows access to related partial components
 */
interface ViewInterface extends RenderableInterface, PartialProviderInterface
{
    /**
     * Returns the SEO title for the view.
     *
     * This title is typically used for HTML <title> tags, browser tabs,
     * and search engine optimization. Should be concise and descriptive
     * of the view's content.
     *
     * @return string The view's title
     */
    public function title(): string;

    /**
     * Returns the SEO description for the view.
     *
     * This description is commonly used for HTML meta description tags
     * and search engine snippets. Should provide a brief, compelling
     * summary of the view's content.
     *
     * @return string The view's description
     */
    public function description(): string;
}