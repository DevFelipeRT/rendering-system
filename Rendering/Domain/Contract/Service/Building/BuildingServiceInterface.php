<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building;

use Rendering\Domain\Contract\Service\Building\Partial\PartialBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Page\PageBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\View\ViewBuildingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;

/**
 * Defines the contract for the high-level Page Building Service.
 *
 * This interface provides a simplified public API for clients (like controllers)
 * to construct a complete Page object. It acts as a Facade, hiding the
 * complexities of the underlying builder and factory components.
 */
interface BuildingServiceInterface extends 
    PartialBuildingServiceInterface, 
    PageBuildingServiceInterface,
    ViewBuildingServiceInterface
{
    /**
     * Sets the layout template for the page.
     *
     * @param string $layout The path to the layout template file.
     * @return self
     */
    public function setPage(string $layout, array $data = [], array $partials = []): self;

    /**
     * Sets the assets (CSS/JS) for the page.
     *
     * This method allows the builder to specify which assets should be included
     * in the page, such as stylesheets and JavaScript files.
     *
     * @param array $assets An array of asset paths.
     * @return self
     */
    public function setAssets(array $assets): self;

    /**
     * Sets the view component.
     *
     * @param string $templateFile The view's template file identifier.
     * @param array<string, mixed> $data The data to be passed to the view template.
     * @param array $partials A nested array defining sub-partials for this view.
     * @return $this
     */
    public function setView(string $templateFile, array $data = [], array $partials = []): self;

    /**
     * Sets the title for the view.
     *
     * @param string $title The title of the view.
     * @return self
     */
    public function setTitle(string $title): self;

    /**
     * Sets the description for the view.
     *
     * @param string $description The description of the view.
     * @return self
     */
    public function setDescription(string $description): self;

    /**
     * Sets the header component.
     *
     * @param string $templateFile The template file for the header.
     * @param array  $data         Optional data for the header template.
     * @param array  $partials     Optional nested partials within the header.
     * @return self
     */
    public function setHeader(string $templateFile, array $data = [], array $partials = []): self;
    
    /**
     * Sets the navigation component for the header.
     *
     * @param string $templateFile The template file for the navigation.
     * @param array  $data         Optional data for the navigation template.
     * @param array  $partials     Optional nested partials within the navigation.
     * @return self
     */
    public function setNavigation(string $templateFile, array $data = [], array $partials = []): self;

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
     * Sets the footer component.
     *
     * @param string $templateFile The template file for the footer.
     * @param array  $data         Optional data for the footer template.
     * @param array  $partials     Optional nested partials within the footer.
     * @return self
     */
    public function setFooter(string $templateFile, array $data = [], array $partials = []): self;

    /**
     * Sets the copyright notice for the footer.
     *
     * @param string $owner The name of the copyright holder.
     * @param string $message The message to append after the owner.
     * @return $this
     */
    public function setCopyright(string $owner, string $message = 'All rights reserved.'): self;

    public function build(): PageInterface;
}
