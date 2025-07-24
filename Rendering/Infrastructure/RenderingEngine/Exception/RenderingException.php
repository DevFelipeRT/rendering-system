<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Exception;

use RuntimeException;
use Throwable;

/**
 * A custom exception for errors during the rendering process, created via static factories.
 *
 * Extends RuntimeException, as these errors occur during program execution.
 * This class uses named constructors to provide clear, context-specific error messages.
 */
class RenderingException extends RuntimeException
{
    /**
     * The constructor is protected to enforce the use of static factory methods.
     */
    protected function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Creates an exception for failures during template path resolution.
     */
    public static function forTemplateResolution(string $templateName, Throwable $previous): self
    {
        $message = sprintf('Failed to resolve template path for "%s".', $templateName);
        return new self($message, 0, $previous);
    }

    /**
     * Creates an exception for failures during the template engine execution.
     */
    public static function forEngineExecution(string $templatePath, Throwable $previous): self
    {
        $message = sprintf('Template engine failed while executing compiled template "%s".', basename($templatePath));
        return new self($message, 0, $previous);
    }
    
    /**
     * Creates an exception for other, unexpected failures within the rendering pipeline.
     */
    public static function forGenericFailure(string $templateName, Throwable $previous): self
    {
        $message = sprintf('An unexpected error occurred while preparing to render template "%s".', $templateName);
        return new self($message, 0, $previous);
    }
}
