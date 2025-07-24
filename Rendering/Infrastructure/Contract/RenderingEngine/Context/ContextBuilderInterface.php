<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Context;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a specialized builder responsible for creating all
 * context objects required for rendering.
 *
 * A class implementing this interface encapsulates the logic for building both the
 * RenderContext (for template data) and the ApiContext (for the ViewApi)
 * from a specific Renderable type.
 */
interface ContextBuilderInterface
{
    /**
     * Builds all necessary context objects for a given renderable.
     *
     * This method performs the main construction logic, producing both the
     * RenderContext and the ApiContext in a single, atomic operation to ensure
     * efficiency and consistency.
     *
     * @param RenderableInterface $renderable The object to build the context for.
     * @return array{0: \Rendering\Infrastructure\Contract\RenderingEngine\Context\RenderContextInterface, 1: \Rendering\Infrastructure\Contract\RenderingEngine\Api\Context\ApiContextInterface} A tuple containing the RenderContext and the ApiContext.
     */
    public function build(RenderableInterface $renderable): array;
}