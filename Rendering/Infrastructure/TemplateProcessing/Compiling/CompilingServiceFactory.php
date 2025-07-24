<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling;

use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\CompilerResolver;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\DirectiveCompilerFactory;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\DirectiveCompilingService;

/**
 * Factory responsible for creating and wiring the template compiling service
 * with all its dependencies.
 * 
 * This factory encapsulates the instantiation logic for the compiling subsystem,
 * reducing complexity in the main kernel.
 */
final class CompilingServiceFactory
{
    /**
     * Creates a fully configured TemplateCompilingService with all its dependencies.
     *
     * @param PathResolvingServiceInterface $pathResolvingService The path resolving service dependency.
     * 
     * @return CompilingServiceInterface The configured compiling service.
     */
    public static function create(
        PathResolvingServiceInterface $pathResolvingService,
        bool $debug = false
    ): CompilingServiceInterface {
        $compilerFactory = new DirectiveCompilerFactory();
        $directiveCompilerResolver = new CompilerResolver($compilerFactory, $pathResolvingService, $debug);
        $directiveCompiler = new DirectiveCompilingService($directiveCompilerResolver);
        
        return new TemplateCompilingService(
            $directiveCompiler
        );
    }
}
