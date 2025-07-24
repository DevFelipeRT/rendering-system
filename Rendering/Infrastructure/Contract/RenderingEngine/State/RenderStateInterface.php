<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\State;

/**
 * Defines the contract for a state management object for a single render job.
 *
 * This interface provides the methods necessary to manage the ephemeral state
 * during the rendering process, including layout hierarchy, sections, and stacks.
 * The state object is responsible for tracking all data collected during the
 * POPULATE stage to be used in the PRESENT stage.
 */
interface RenderStateInterface
{
    /**
     * Sets the parent layout for the current inheritance chain.
     *
     * @param string|null $layoutName The name of the parent layout.
     */
    public function setParent(string|null $layoutName): void;

    /**
     * Retrieves the last declared parent layout.
     *
     * @return string|null The name of the parent layout or null if none was set.
     */
    public function getParent(): ?string;

    /**
     * Starts capturing content for a named section.
     *
     * @param string $sectionName The unique name of the section.
     */
    public function startSection(string $sectionName): void;

    /**
     * Stops capturing content for the last opened section and stores it.
     */
    public function stopSection(): void;

    /**
     * Retrieves the final content of a named section.
     *
     * @param string $sectionName The name of the section.
     * @return string The content of the section, or an empty string if not found.
     */
    public function getSection(string $sectionName): string;

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
     * Retrieves the complete, concatenated content of a named stack.
     *
     * @param string $stackName The name of the stack.
     * @return string The content of the stack, or an empty string if not found.
     */
    public function renderStack(string $stackName): string;

    /**
     * Determines if a block identified by a unique ID should be processed.
     *
     * @param string $id A unique identifier for the @once block.
     * @return bool True if the block should be rendered, false otherwise.
     */
    public function shouldRenderOnce(string $id): bool;
}