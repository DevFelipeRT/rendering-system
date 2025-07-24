<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

/**
 * The ViewApi implementation for the state population stage.
 *
 * It extends the AbstractViewApi and implements the logic for directives that
 * write to the RenderState, such as defining hierarchy, sections, and stacks.
 * Methods related to the presentation stage are implemented as no-ops.
 */
final class PopulatingViewApi extends AbstractViewApi
{
    /**
     * {@inheritdoc}
     *
     * Declares the parent layout for the current template.
     */
    public function extend(string $layoutName): void
    {
        $this->renderState->setParent($layoutName);
    }

    /**
     * {@inheritdoc}
     *
     * Starts capturing content for a named section.
     */
    public function startSection(string $sectionName): void
    {
        $this->renderState->startSection($sectionName);
    }

    /**
     * {@inheritdoc}
     *
     * Stops capturing content for the last opened section.
     */
    public function stopSection(): void
    {
        $this->renderState->stopSection();
    }

    /**
     * {@inheritdoc}
     *
     * This method is a no-op during the population stage.
     */
    public function yieldSection(string $sectionName): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * Starts buffering content for a named stack.
     */
    public function startPush(string $stackName): void
    {
        $this->renderState->startPush($stackName);
    }

    /**
     * {@inheritdoc}
     *
     * Stops buffering content and adds it to the appropriate stack.
     */
    public function stopPush(): void
    {
        $this->renderState->stopPush();
    }

    /**
     * {@inheritdoc}
     *
     * This method is a no-op during the population stage.
     */
    public function renderStack(string $stackName): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * Determines if a block identified by a unique ID should be processed.
     */
    public function shouldRenderOnce(string $id): bool
    {
        return $this->renderState->shouldRenderOnce($id);
    }
}