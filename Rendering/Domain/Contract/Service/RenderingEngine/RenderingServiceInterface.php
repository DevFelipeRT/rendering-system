<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\RenderingEngine;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;

/**
 * Defines the contract for the main rendering orchestrator service.
 *
 * This interface represents the primary entry point for all rendering operations,
 * including stateful functionalities like stacks and once-blocks, which are
 * needed by the template's ViewApi.
 */
interface RenderingServiceInterface
{
    /**
     * Renders a renderable domain object.
     *
     * @param RenderableInterface $renderable The object to render.
     * @return string The final rendered content.
     */
    public function render(RenderableInterface $renderable): string;

    /**
     * Renders a template file directly.
     *
     * @param string $templateFile The path or name of the template.
     * @param array $templateData Data to be made available to the template.
     * @return string The final rendered content.
     */
    public function renderTemplate(string $templateFile, array $templateData = []): string;

    /**
     * Starts buffering content for a named stack.
     *
     * @param string $stackName The name of the stack.
     */
    public function startPush(string $stackName): void;

    /**
     * Stops buffering content and adds it to the appropriate stack.
     */
    public function stopPush(): void;

    /**
     * Renders the complete content of a named stack.
     *
     * @param string $stackName The name of the stack to render.
     * @return string The concatenated content of the stack.
     */
    public function renderStack(string $stackName): string;

    /**
     * Determines if a block identified by a unique ID should be rendered.
     *
     * @param string $id A unique identifier for the @once block.
     * @return bool True if the block should be rendered, false otherwise.
     */
    public function shouldRenderOnce(string $id): bool;

    /**
     * Returns the current rendering stage (e.g., 'POPULATE' or 'PRESENT').
     *
     * @return string The active rendering stage.
     */
    public function getCurrentStage(): string;

    /**
     * Returns the active state object for the current render job.
     *
     * @return RenderStateInterface The active state object.
     */
    public function getActiveState(): RenderStateInterface;
}