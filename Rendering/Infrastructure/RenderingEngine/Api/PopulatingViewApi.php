<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

/**
 * The ViewApi implementation for the state population stage.
 */
final class PopulatingViewApi extends AbstractViewApi
{
    /**
     * {@inheritdoc}
     */
    public function extend(string $layoutName): void
    {
        $this->renderState->setParent($layoutName);
    }

    /**
     * {@inheritdoc}
     */
    public function startSection(string $sectionName): void
    {
        $this->renderState->startSection($sectionName);
    }

    /**
     * {@inheritdoc}
     */
    public function stopSection(): void
    {
        $this->renderState->stopSection();
    }

    /**
     * {@inheritdoc}
     *
     * During the POPULATE stage, this method delegates to the RenderState
     * to create and register a unique placeholder for the yielded section.
     * This ID is returned and embedded in the parent section's content.
     */
    public function yieldSection(string $sectionName): string
    {
        return $this->renderState->registerYield($sectionName);
    }

    /**
     * {@inheritdoc}
     */
    public function startPush(string $stackName): void
    {
        $this->renderState->startPush($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPush(): void
    {
        $this->renderState->stopPush();
    }

    /**
     * {@inheritdoc}
     */
    public function renderStack(string $stackName): string
    {
        // No-op during population
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderOnce(string $id): bool
    {
        return $this->renderState->shouldRenderOnce($id);
    }
}
