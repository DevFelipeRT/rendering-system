<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Api;

use Rendering\Domain\Contract\Service\RenderingEngine\Api\ViewApiInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a service that provides configured ViewApi instances.
 */
interface ViewApiProvidingServiceInterface
{
    /**
     * Provides a ViewApi instance configured for a specific renderable object.
     *
     * This method will query the provided rendering service to determine the
     * current stage and active state needed to construct the correct ViewApi.
     *
     * @param RenderableInterface $renderable The object being rendered.
     * @param RenderingServiceInterface $renderingService The rendering service instance making the call.
     * @return ViewApiInterface A configured ViewApi instance appropriate for the stage.
     */
    public function provideFor(
        RenderableInterface $renderable,
        RenderingServiceInterface $renderingService
    ): ViewApiInterface;

    /**
     * Provides an empty ViewApi instance for the current rendering stage.
     *
     * This method is useful for cases where a renderable object is not available,
     * but a ViewApi is still needed (e.g., for layout rendering).
     *
     * @param RenderingServiceInterface $renderingService The rendering service instance making the call.
     * @return ViewApiInterface A configured ViewApi instance appropriate for the stage.
     */
    public function provideEmpty(
        RenderingServiceInterface $renderingService
    ): ViewApiInterface;
}