<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Context;

use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextInterface;

/**
 * Defines the contract for the DTO that holds the context for the ViewApi.
 * It extends the base ContextInterface for common data access methods.
 */
interface ApiContextInterface extends ContextInterface
{
    /**
     * Gets the API context objects.
     * This is a specific alias for the all() method for clarity.
     *
     * @return array An array of RenderableInterface instances.
     */
    public function getContextObjects(): array;
}