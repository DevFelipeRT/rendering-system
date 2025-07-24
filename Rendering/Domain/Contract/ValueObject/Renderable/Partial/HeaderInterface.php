<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;

/**
 * Contract for header partial view components.
 *
 * This interface extends PartialViewInterface to define the contract
 * for header components that are typically rendered at the top
 * of pages. Header components often contain navigation menus,
 * site branding, user information, breadcrumbs, and other
 * primary navigation elements.
 *
 * By implementing this interface, header components ensure they
 * can be properly rendered within the page composition system
 * while maintaining type safety and architectural consistency.
 */
interface HeaderInterface extends PartialViewInterface
{
    /**
     * Retrieves the navigation menu object for the header.
     *
     * This method provides access to navigation links and menu
     * items that should be rendered in the header component.
     *
     * @return NavigationInterface|null An instance of NavigationInterface containing navigation data, or null if not set
     */
    public function navigation(): ?NavigationInterface;
}