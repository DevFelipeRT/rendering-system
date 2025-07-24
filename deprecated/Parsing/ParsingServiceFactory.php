<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Parsing;

use Rendering\Infrastructure\Contract\TemplateProcessing\Parsing\ParsingServiceInterface;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Parsing\Parser\LayoutParser;
use Rendering\Infrastructure\TemplateProcessing\Parsing\Parser\SectionParser;

/**
 * Factory responsible for creating and wiring the template parsing service
 * with all its dependencies.
 * 
 * This factory encapsulates the instantiation logic for the parsing subsystem,
 * reducing complexity in the main kernel.
 */
final class ParsingServiceFactory
{
    /**
     * Creates a fully configured TemplateParsingService with all its dependencies.
     *
     * @param PathResolvingServiceInterface $pathResolver The service to resolve template paths.
     * @return ParsingServiceInterface The configured parsing service.
     */
    public static function create(PathResolvingServiceInterface $pathResolver): ParsingServiceInterface
    {
        $layoutParser = new LayoutParser();
        $sectionParser = new SectionParser();
        
        return new TemplateParsingService(
            $layoutParser,
            $sectionParser,
            $pathResolver
        );
    }
}
