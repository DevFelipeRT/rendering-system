<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\RenderingEngine;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a stateless rendering "worker".
 *
 * A class implementing this interface is responsible for the final step of
 * rendering: taking a template and a fully prepared data array and executing it.
 */
interface RendererInterface
{
    /**
     * Renders a renderable object with its pre-prepared data array.
     *
     * @param RenderableInterface $renderable The object to be rendered.
     * @param array $data The fully prepared data for the renderable's scope.
     * @return string The rendered content.
     */
    public function render(RenderableInterface $renderable, array $data): string;

    /**
     * Renders a template file with a pre-prepared data array.
     *
     * @param string $templateFile The path or name of the template.
     * @param array $data The fully prepared data for the template's scope.
     * @return string The rendered content.
     */
    public function renderTemplate(string $templateFile, array $data = []): string;
}