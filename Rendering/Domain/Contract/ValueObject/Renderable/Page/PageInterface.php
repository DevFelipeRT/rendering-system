<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Page;

use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\AssetsInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialProviderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a composite Page Value Object.
 *
 * A Page represents the complete renderable document structure, aggregating
 * all its component parts into a cohesive unit. It acts as the top-level
 * container that orchestrates the rendering of header, main view content,
 * footer, and associated assets.
 *
 * Key Architectural Features:
 * - Composes a mandatory ViewInterface as the central content
 * - Inherits SEO metadata (title, description) from the contained view
 * - Optionally includes structural components (header, footer, assets)
 * - Provides access to partial components through PartialProviderInterface
 * - Maintains the complete page rendering context
 *
 * The Page serves as the primary entry point for full-page rendering
 * operations and ensures all necessary components are available for
 * template processing.
 *
 * @extends RenderableInterface Provides core rendering capabilities
 * @extends PartialProviderInterface Allows access to injectable partial components
 */
interface PageInterface extends RenderableInterface, PartialProviderInterface
{
    /**
     * Returns the page title inherited from the primary view.
     *
     * This method delegates to the contained view's title() method,
     * ensuring consistency between the page and its main content.
     *
     * @return string The title from the primary view
     */
    public function title(): string;

    /**
     * Returns the page description inherited from the primary view.
     *
     * This method delegates to the contained view's description() method,
     * providing SEO metadata that reflects the main content being displayed.
     *
     * @return string The description from the primary view
     */
    public function description(): string;
    
    /**
     * Returns the optional header component of the page.
     *
     * @return HeaderInterface|null The header component, or null if not present
     */
    public function header(): ?HeaderInterface;

    /**
     * Returns the mandatory primary view content of the page.
     *
     * The view is the core content component and is always required
     * for a complete page. All SEO metadata is inherited from this view.
     *
     * @return ViewInterface The primary view content (never null)
     */
    public function view(): ViewInterface;

    /**
     * Returns the optional footer component of the page.
     *
     * @return FooterInterface|null The footer component, or null if not present
     */
    public function footer(): ?FooterInterface;

    /**
     * Returns the optional assets associated with the page.
     *
     * Assets include CSS stylesheets, JavaScript files, and other
     * resources needed for proper page rendering and functionality.
     * Assets are represented as immutable data objects that can be
     * injected into templates for rendering.
     * 
     * @return AssetsInterface|null The assets data object, or null if no assets are defined
     */
    public function assets(): ?AssetsInterface;
}
