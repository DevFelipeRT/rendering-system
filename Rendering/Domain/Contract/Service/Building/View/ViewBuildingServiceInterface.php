<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\View;

use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;

/**
 * Defines the contract for the View Building Service.
 *
 * This service focuses on the creation of view components,
 * providing factory methods that orchestrate the appropriate builders.
 * It offers a simplified API for common view construction scenarios while
 * maintaining access to specialized builders for complex cases.
 */
interface ViewBuildingServiceInterface
{
    /**
     * Creates a view component.
     *
     * @param string $templateFile The template file path.
     * @param array $data The template data.
     * @param array $partials Nested partials in various formats.
     * @param string|null $title The view title (optional).
     * @param string|null $description The view description (optional).
     * @return ViewInterface The constructed view.
     * @throws \LogicException If construction fails.
     */
    public function createView(
        string $templateFile, 
        array $data = [], 
        array $partials = [],
        ?string $title = null,
        ?string $description = null
    ): ViewInterface;

    /**
     * Gets the view builder for advanced construction scenarios.
     *
     * @return ViewBuilderInterface The view builder.
     */
    public function viewBuilder(): ViewBuilderInterface;
}
