<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Engine;

use Rendering\Infrastructure\Contract\RenderingEngine\TemplateEngineInterface;
use Rendering\Infrastructure\RenderingEngine\Exception\TemplateEngineException;
use Throwable;

/**
 * A low-level PHP template execution engine.
 *
 * Its sole responsibility is to execute a given PHP template file in an isolated
 * scope, inject variables, and capture the output buffer.
 */
final class PhpTemplateEngine implements TemplateEngineInterface
{
    /**
     * Renders a template file with the given data.
     *
     * {@inheritdoc}
     */
    public function execute(string $templatePath, array $data): string
    {
        $this->assertTemplateExists($templatePath);

        try {
            $renderCallable = $this->createTemplateExecutionCallable($templatePath, $data);
            return $this->captureOutput($renderCallable);
        } catch (Throwable $e) {
            throw TemplateEngineException::forExecutionFailure($templatePath, $e);
        }
    }

    /**
     * Validates that a template file exists and is readable.
     *
     * @throws TemplateEngineException If the file is not found or is not readable.
     */
    private function assertTemplateExists(string $templatePath): void
    {
        if (!is_file($templatePath)) {
            throw TemplateEngineException::forNotFound($templatePath);
        }
        
        if (!is_readable($templatePath)) {
            throw TemplateEngineException::forNotReadable($templatePath);
        }
    }

    /**
     * Creates a callable that renders a template in a completely isolated scope.
     *
     * @return callable A zero-argument callable ready for execution.
     */
    private function createTemplateExecutionCallable(string $templatePath, array $data): callable
    {
        return function () use ($templatePath, $data): void {
            (static function (): void {
                extract(func_get_arg(1), EXTR_SKIP);
                include func_get_arg(0);
            })($templatePath, $data);
        };
    }

    /**
     * Captures the output of a given operation using output buffering.
     *
     * @throws Throwable Re-throws any exception that occurs during the operation.
     * @throws TemplateEngineException If the output capture fails.
     */
    private function captureOutput(callable $operation): string
    {
        ob_start();
        try {
            $operation();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $output = ob_get_clean();
        
        if ($output === false) {
            throw TemplateEngineException::forOutputCaptureFailure();
        }
        
        return $output;
    }
}
