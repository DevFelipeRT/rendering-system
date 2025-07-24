<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling;

use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilingServiceInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\DirectiveCompilingServiceInterface;

/**
 * Compiles template content by delegating to the directive compilation pipeline.
 *
 * This service acts as a pure transformation engine, receiving a raw template
 * string and returning a string of executable PHP code.
 */
final class TemplateCompilingService implements CompilingServiceInterface
{
    /**
     * @param DirectiveCompilingServiceInterface $directiveCompiler The service that runs the directive pipeline.
     */
    public function __construct(
        private readonly DirectiveCompilingServiceInterface $directiveCompiler
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function compile(string $content): string
    {
        if (empty(trim($content))) {
            return '';
        }

        return $this->directiveCompiler->compileDirectives($content);
    }
}