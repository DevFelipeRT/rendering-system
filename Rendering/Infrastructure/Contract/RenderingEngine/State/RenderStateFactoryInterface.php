<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\State;

/**
 * Defines the contract for a factory that creates RenderState instances.
 *
 * This allows a service like the RenderingService to be decoupled from the
 * concrete implementation of the RenderState object, improving testability.
 */
interface RenderStateFactoryInterface
{
    /**
     * Creates a new, clean instance that implements RenderStateInterface.
     *
     * @return RenderStateInterface The new state object for a rendering job.
     */
    public function create(): RenderStateInterface;
}