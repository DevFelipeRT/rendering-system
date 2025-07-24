<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\RenderingEngine\Api;

/**
 * Defines the unified, public-facing API available within templates.
 *
 * This contract provides all possible methods that can be called from a compiled
 * template. The concrete implementation will decide how to handle each call
 * based on the current rendering stage (e.g., POPULATE or PRESENT).
 */
interface ViewApiInterface
{
    /**
     * Declares the parent layout for the current template.
     * This is primarily active during the state population stage.
     *
     * @param string $layoutName The name of the parent layout template.
     */
    public function extend(string $layoutName): void;

    /**
     * Starts capturing content for a named section.
     * This is primarily active during the state population stage.
     *
     * @param string $sectionName The unique name of the section.
     */
    public function startSection(string $sectionName): void;

    /**
     * Stops capturing content for the last opened section.
     * This is primarily active during the state population stage.
     */
    public function stopSection(): void;

    /**
     * Renders the content of a named section from the populated state.
     * This is primarily active during the presentation stage.
     *
     * @param string $sectionName The name of the section to render.
     * @return string The captured content of the section.
     */
    public function yieldSection(string $sectionName): string;

    /**
     * Starts buffering content for a named stack.
     * This is primarily active during the state population stage.
     *
     * @param string $stackName The name of the stack.
     */
    public function startPush(string $stackName): void;

    /**
     * Stops buffering content and adds it to the appropriate stack.
     * This is primarily active during the state population stage.
     */
    public function stopPush(): void;

    /**
     * Renders the complete content of a named stack from the populated state.
     * This is primarily active during the presentation stage.
     *
     * @param string $stackName The name of the stack to render.
     * @return string The concatenated content of the stack.
     */
    public function renderStack(string $stackName): string;

    /**
     * Determines if a block identified by a unique ID should be processed.
     * This is primarily active during the state population stage.
     *
     * @param string $id A unique identifier for the @once block.
     * @return bool True if the block should be rendered, false otherwise.
     */
    public function shouldRenderOnce(string $id): bool;

    /**
     * Includes another template.
     * The behavior (e.g., returning content or not) is determined by the
     * rendering stage.
     *
     * @param string $templateFile The name of the template to include.
     * @param array $data Additional data to pass to the template's scope.
     * @return string The rendered content, if in the presentation stage.
     */
    public function include(string $templateFile, array $data = []): string;

    /**
     * Renders a partial view identified by a unique key.
     * The behavior is determined by the rendering stage.
     *
     * @param string $identifier The unique identifier for the partial.
     * @param array $data Additional data to pass to the partial's scope.
     * @return string The rendered content, if in the presentation stage.
     */
    public function renderPartial(string $identifier, array $data = []): string;
}