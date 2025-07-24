<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkInterface;
use Rendering\Domain\Contract\Service\Building\Exception\BuilderExceptionInterface;

/**
 * Defines the contract for a Navigation component builder.
 *
 * This interface provides a fluent API for constructing a Navigation object,
 * allowing for the step-by-step assembly of navigation links and other
 * partial components.
 */
interface NavigationBuilderInterface extends PartialBuilderInterface
{
    /**
     * Appends a single link to the navigation menu.
     *
     * This method is ideal for programmatically adding individual links to the
     * navigation menu in a step-by-step fashion.
     *
     * @example
     * $builder->addNavigationLink('Home', '/', true, true, 'bi bi-house');
     *
     * @param string $label     The visible text for the link (e.g., "Home").
     * @param string $url       The destination URL (e.g., "/dashboard").
     * @param bool   $visible   Controls if the link should be rendered. Defaults to true.
     * @param bool   $active    Marks the link as the current page for styling. Defaults to false.
     * @param string $iconClass Optional CSS classes for an icon. Defaults to an empty string.
     *
     * @return self The builder instance for fluent method chaining.
     */
    public function addNavigationLink(
        string $label,
        string $url,
        bool $visible = true,
        bool $active = false,
        string $iconClass = ''
    ): self;

    /**
     * Sets the entire collection of navigation links at once, replacing any existing links.
     *
     * This is the preferred method for populating the navigation from a predefined
     * array structure, such as a configuration file or a database result.
     *
     * @example
     * $builder->setNavigationLinks([
     * ['label' => 'Profile', 'url' => '/profile'],
     * new NavigationLink('/logout', 'Logout')
     * ]);
     *
     * @param array<int, array<string, mixed>|NavigationLinkInterface> $links
     * An array where each item is either an associative array of link data
     * (e.g., ['label' => 'Home', 'url' => '/']) or a pre-built
     * `NavigationLinkInterface` object.
     *
     * @return self The builder instance for fluent method chaining.
     */
    public function setNavigationLinks(array $links): self;

    /**
     * Assembles the final, immutable Navigation object from the builder's current state.
     *
     * This is the terminal method in the build process and should only be called
     * after all links and other configurations have been set.
     *
     * @return NavigationInterface The fully constructed, ready-to-render navigation object.
     *
     * @throws BuilderExceptionInterface If the builder is not in a "ready" state,
     * for instance, if no template or links have been provided.
     */
    public function build(): NavigationInterface;
}
