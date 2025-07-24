<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive;

use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilerFactoryInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilerInterface;
use LogicException;

/**
 * A pure factory responsible for instantiating compiler objects.
 *
 * This class adheres strictly to the factory pattern. Its single responsibility
 * is to create a new compiler instance when provided with a fully-qualified
 * class name and an ordered list of constructor arguments. It does not contain
 * any business logic related to dependency resolution or caching.
 */
final class DirectiveCompilerFactory implements CompilerFactoryInterface
{
    /**
     * Creates a new compiler instance.
     *
     * @param class-string<CompilerInterface> $compilerClass The fully-qualified class name of the compiler to instantiate.
     * @param list<mixed> $constructorArgs An ordered list of arguments for the compiler's constructor.
     * @return CompilerInterface The newly created compiler instance.
     * @throws LogicException If the specified class does not exist or does not implement CompilerInterface.
     */
    public function create(string $compilerClass, array $constructorArgs = []): CompilerInterface
    {
        if (!class_exists($compilerClass)) {
            throw new LogicException("Cannot create compiler: Class '{$compilerClass}' does not exist.");
        }

        $compiler = new $compilerClass(...$constructorArgs);

        if (!$compiler instanceof CompilerInterface) {
            throw new LogicException(
                "The class '{$compilerClass}' must implement the " . CompilerInterface::class . " interface."
            );
        }

        return $compiler;
    }
}
