<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing;

use Rendering\Domain\ValueObject\Shared\Directory;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\TemplateProcessingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Compiling\CompilingServiceFactory;
use Rendering\Infrastructure\TemplateProcessing\Tool\TemplateCache;

/**
 * Factory responsible for creating and wiring the complete template processing service
 * with all its dependencies.
 * 
 * This factory encapsulates the instantiation logic for the entire template processing
 * subsystem, including parsing, compiling, caching, and path resolution.
 */
final class ProcessingServiceFactory
{
    /**
     * Creates a fully configured TemplateProcessingService with all its dependencies.
     *
     * @param Directory $cacheDirectory The validated cache directory for compiled templates.
     * @param PathResolvingServiceInterface $pathResolver The service for resolving paths to templates, JS, CSS, etc.
     * @param bool $debugMode When true, templates are recompiled on every request.
     * 
     * @return TemplateProcessingServiceInterface The configured template processing service.
     */
    public static function create(
        Directory $cacheDirectory,
        PathResolvingServiceInterface $pathResolver,
        bool $debugMode = false
    ): TemplateProcessingServiceInterface {;
        $templateCompiler = CompilingServiceFactory::create($pathResolver, $debugMode);
        $templateCache = new TemplateCache($cacheDirectory);
        
        return new TemplateProcessingService(
            $pathResolver,
            $templateCache,
            $templateCompiler,
            $debugMode
        );
    }
}
