<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;

/**
 * Defines the contract for the Partial Building Service.
 *
 * This service focuses on the creation of partial components,
 * providing factory methods that orchestrate the appropriate builders.
 * It offers a simplified API for common construction scenarios while
 * maintaining access to specialized builders for complex cases.
 */
interface PartialBuildingServiceInterface
{
    /**
     * Creates a header component.
     *
     * @param string $templateFile The template file path.
     * @param array $data The template data.
     * @param array $partials Nested partials in various formats.
     * @param NavigationInterface|null $navigation Optional navigation component.
     * 
     * @return HeaderInterface The constructed header.
     */
    public function createHeader(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        ?NavigationInterface $navigation = null
    ): HeaderInterface;

    /**
     * Creates a footer component.
     *
     * @param string $templateFile The template file path.
     * @param array $data The template data.
     * @param array $partials Nested partials in various formats.
     * @param string|null $copyrightOwner The owner of the copyright notice.
     * @param string|null $copyrightMessage The message for the copyright notice.
     * 
     * @return FooterInterface The constructed footer.
     */
    public function createFooter(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        ?string $copyrightOwner = null,
        ?string $copyrightMessage = 'All rights reserved.'
    ): FooterInterface;

    /**
     * Creates a navigation component.
     *
     * @param string $templateFile The template file path.
     * @param array $data The template data.
     * @param array $partials Nested partials in various formats.
     * @param array $links An array of navigation links, each containing label, URL, and active state.
     * 
     * @return NavigationInterface The constructed navigation.
     */
    public function createNavigation(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        array $links = []
    ): NavigationInterface;

    /**
     * Creates a generic partial view.
     *
     * @param string $templateFile The template file path.
     * @param array $data The template data.
     * @param array $partials Nested partials in various formats.
     * 
     * @return PartialViewInterface The constructed partial view.
     */
    public function createPartialView(
        string $templateFile, 
        array $data = [], 
        array $partials = []
    ): PartialViewInterface;

    /**
     * Gets the header builder for advanced construction scenarios.
     *
     * @return HeaderBuilderInterface The header builder.
     */
    public function headerBuilder(): HeaderBuilderInterface;

    /**
     * Gets the footer builder for advanced construction scenarios.
     *
     * @return FooterBuilderInterface The footer builder.
     */
    public function footerBuilder(): FooterBuilderInterface;

    /**
     * Gets the navigation builder for advanced construction scenarios.
     *
     * @return NavigationBuilderInterface The navigation builder.
     */
    public function navigationBuilder(): NavigationBuilderInterface;

    /**
     * Gets the partial builder for advanced construction scenarios.
     *
     * @return PartialBuilderInterface The partial builder.
     */
    public function partialBuilder(): PartialBuilderInterface;
}