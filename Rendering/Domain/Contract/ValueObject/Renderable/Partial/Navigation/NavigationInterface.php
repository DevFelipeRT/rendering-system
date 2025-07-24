<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;

/**
 * Contract for navigation partial view components.
 *
 * This interface extends PartialViewInterface to define the contract
 * for navigation components that manage and render navigation menus.
 * Implementations must provide access to navigation link collections
 * for menu rendering purposes.
 */
interface NavigationInterface extends PartialViewInterface
{
    /**
     * Retrieves the collection of navigation links.
     *
     * @return NavigationLinkCollectionInterface The collection of navigation links that will be rendered in the navigation menu.
     */
    public function links(): NavigationLinkCollectionInterface;
}