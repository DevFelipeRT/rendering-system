<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\RenderingEngine\Context;

/**
 * Defines the contract for the DTO that holds template-specific data.
 * It extends the base ContextInterface for common data access methods.
 */
interface RenderContextInterface extends ContextInterface
{
    /**
     * Gets the final data array to be injected into the template's scope.
     * This is a specific alias for the all() method for clarity.
     *
     * @return array<string, mixed> An associative array of variables.
     */
    public function getData(): array;
}