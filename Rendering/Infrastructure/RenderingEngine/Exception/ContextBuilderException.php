<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Exception;

use RuntimeException;

/**
 * A custom exception for errors within the ContextBuilder.
 *
 * Extends RuntimeException and uses a named constructor to provide a clear
 * error message when a suitable builder cannot be found for a renderable type.
 */
class ContextBuilderException extends RuntimeException
{
    /**
     * Creates an exception for when no suitable builder is found for a renderable type.
     *
     * @param string $renderableClass The class name of the renderable object.
     */
    public static function forNotFound(string $renderableClass): self
    {
        $message = sprintf('No context builder registered for renderable of type "%s".', $renderableClass);
        return new self($message);
    }
}
