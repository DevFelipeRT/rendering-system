<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Exception;

use RuntimeException;
use Throwable;

/**
 * A custom exception for errors within the PhpTemplateEngine.
 *
 * Extends RuntimeException and uses named constructors to provide clear,
 * context-specific error messages for different failure scenarios.
 */
class TemplateEngineException extends RuntimeException
{
    /**
     * The constructor is protected to enforce the use of static factory methods.
     */
    protected function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Creates an exception for when a template file is not found.
     */
    public static function forNotFound(string $path): self
    {
        $message = sprintf('Template file not found at path: %s', $path);
        return new self($message);
    }

    /**
     * Creates an exception for when a template file is not readable.
     */
    public static function forNotReadable(string $path): self
    {
        $message = sprintf('Template file is not readable: %s', $path);
        return new self($message);
    }

    /**
     * Creates an exception for when the output buffer capture fails.
     */
    public static function forOutputCaptureFailure(): self
    {
        return new self('Failed to capture template output buffer.');
    }
    
    /**
     * Creates an exception for failures during the isolated execution of a template.
     */
    public static function forExecutionFailure(string $path, Throwable $previous): self
    {
        $message = sprintf(
            'Error executing template "%s": %s',
            basename($path),
            $previous->getMessage()
        );
        return new self($message, 0, $previous);
    }
}
