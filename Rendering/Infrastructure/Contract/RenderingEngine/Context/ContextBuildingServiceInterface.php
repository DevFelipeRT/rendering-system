<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Context;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ApiContextInterface;

/**
 * Defines the contract for the context building orchestrator service.
 */
interface ContextBuildingServiceInterface
{
    /**
     * Builds the RenderContext for a given renderable object.
     *
     * @param RenderableInterface $renderable The object to build the context for.
     * @return RenderContextInterface The context containing template data.
     */
    public function buildRenderContext(RenderableInterface $renderable): RenderContextInterface;

    /**
     * Builds the ApiContext for a given renderable object.
     *
     * @param RenderableInterface $renderable The object to build the context for.
     * @return ApiContextInterface The context for the ViewApi.
     */
    public function buildApiContext(RenderableInterface $renderable): ApiContextInterface;
}