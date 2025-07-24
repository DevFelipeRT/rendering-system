<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine;

use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\TemplateProcessingServiceInterface;
use Rendering\Infrastructure\RenderingEngine\Api\ViewApiProvidingServiceFactory;
use Rendering\Infrastructure\RenderingEngine\Context\ContextBuildingServiceFactory;
use Rendering\Infrastructure\RenderingEngine\Engine\PhpTemplateEngine;
use Rendering\Infrastructure\RenderingEngine\Renderer\PageRenderer;
use Rendering\Infrastructure\RenderingEngine\Renderer\Renderer;
use Rendering\Infrastructure\RenderingEngine\State\RenderStateFactory;

/**
 * Factory responsible for creating a fully configured RenderingService.
 *
 * This class encapsulates the logic of assembling the high-level dependencies
 * required by the rendering service.
 */
final class RenderingServiceFactory
{
    /**
     * Creates a new instance of RenderingServiceInterface with all necessary dependencies.
     *
     * @param TemplateProcessingServiceInterface $templateProcessor
     * @param bool $debug
     * @return RenderingServiceInterface
     */
    public static function create(
        TemplateProcessingServiceInterface $templateProcessor,
        bool $debug = false
    ): RenderingServiceInterface {
        $renderStateFactory = new RenderStateFactory();
        $contextBuildingServiceFactory = new ContextBuildingServiceFactory();
        $viewApiProvidingServiceFactory = new ViewApiProvidingServiceFactory();

        $contextBuildingService = $contextBuildingServiceFactory->create();
        $viewApiProvidingService = $viewApiProvidingServiceFactory->create($contextBuildingService);
        $templateEngine = new PhpTemplateEngine();
        $pageRenderer = new PageRenderer($templateEngine, $templateProcessor, $debug);
        $defaultRenderer = new Renderer($templateEngine, $templateProcessor, $debug);

        return new RenderingService(
            $pageRenderer,
            $defaultRenderer,
            $viewApiProvidingService,
            $contextBuildingService,
            $renderStateFactory
        );
    }
}