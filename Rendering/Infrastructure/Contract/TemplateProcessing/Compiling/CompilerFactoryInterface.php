<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\TemplateProcessing\Compiling;

/**
 * Defines a contract for a factory that dynamically creates compiler instances.
 */
interface CompilerFactoryInterface
{
    /**
     * Creates a compiler instance based on its class name and the available context.
     *
     * @param class-string<CompilerInterface> $compilerClass The compiler's FQCN.
     * @param array<string, mixed> $context Optional data for the compiler's constructor.
     * @return CompilerInterface
     */
    public function create(string $compilerClass, array $context = []): CompilerInterface;
}