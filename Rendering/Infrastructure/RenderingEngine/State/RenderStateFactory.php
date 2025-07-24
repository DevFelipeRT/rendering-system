<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\State;

use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateFactoryInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;

/**
 * A simple factory that constructs new instances of the RenderState class.
 *
 * This class is responsible for creating ephemeral state objects for each
 * top-level rendering operation.
 */
final class RenderStateFactory implements RenderStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(): RenderStateInterface
    {
        return new RenderState();
    }
}