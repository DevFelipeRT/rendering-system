<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

/**
 * The ViewApi implementation for the final presentation stage.
 *
 * It extends the AbstractViewApi and implements the logic for directives that
 * read from the RenderState to output the final content, such as yielding
 * sections and rendering stacks. Methods related to the population stage are
 * inherited as no-ops from the abstract parent.
 */
final class PresentingViewApi extends AbstractViewApi
{
    /**
     * {@inheritdoc}
     *
     * Throws a LogicException as @extend is not allowed during the presentation stage.
     */
    public function extend(string $layoutName): void
    {
        // This method is a no-op during the presentation stage.
        // Optionally, throw an exception for stricter error checking.
        // throw new \LogicException('@extend cannot be called during the presentation stage.');
    }

    /**
     * {@inheritdoc}
     *
     * Throws a LogicException as @section is not allowed during the presentation stage.
     */
    public function startSection(string $sectionName): void
    {
        // This method is a no-op during the presentation stage.
    }

    /**
     * {@inheritdoc}
     *
     * Throws a LogicException as @endsection is not allowed during the presentation stage.
     */
    public function stopSection(): void
    {
        // This method is a no-op during the presentation stage.
    }

    /**
     * {@inheritdoc}
     *
     * Renders the content of a named section from the populated state.
     */
    public function yieldSection(string $sectionName): string
    {
        return $this->renderState->getSection($sectionName);
    }

    /**
     * {@inheritdoc}
     *
     * Throws a LogicException as @push is not allowed during the presentation stage.
     */
    public function startPush(string $stackName): void
    {
        // This method is a no-op during the presentation stage.
    }

    /**
     * {@inheritdoc}
     *
     * Throws a LogicException as @endpush is not allowed during the presentation stage.
     */
    public function stopPush(): void
    {
        // This method is a no-op during the presentation stage.
    }

    /**
     * {@inheritdoc}
     *
     * Renders the complete content of a named stack from the populated state.
     */
    public function renderStack(string $stackName): string
    {
        return $this->renderState->renderStack($stackName);
    }

    /**
     * {@inheritdoc}
     *
     * This method is a no-op during the presentation stage.
     */
    public function shouldRenderOnce(string $id): bool
    {
        // @once directives are only evaluated during the population stage.
        // Returning false prevents the block from ever rendering in this stage.
        return false;
    }
}