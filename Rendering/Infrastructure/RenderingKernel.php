<?php

declare(strict_types=1);

namespace Rendering\Infrastructure;

use Rendering\Infrastructure\Contract\RenderingKernelInterface;
use Rendering\Infrastructure\Contract\RenderingConfigInterface;
use Rendering\Domain\Contract\Service\RenderingFacadeInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\TemplateProcessingServiceInterface;
use Rendering\Infrastructure\Building\BuildingServiceFactory;
use Rendering\Infrastructure\TemplateProcessing\ProcessingServiceFactory;
use Rendering\Application\RenderingFacade;
use Rendering\Domain\Contract\Service\Building\BuildingServiceInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\ValueObject\Shared\Directory;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\PathResolving\PathResolvingFactory;
use Rendering\Infrastructure\RenderingEngine\RenderingServiceFactory;

/**
 * A self-contained bootstrap for the Rendering module.
 *
 * This class acts as the Composition Root for the entire rendering system.
 * It is responsible for instantiating and wiring all components (services,
 * renderers, factories, etc.) with their dependencies. It operates without an
 * external dependency injection container, making it a portable and explicit factory
 * for the module's services.
 */
final class RenderingKernel implements RenderingKernelInterface
{
    /** The validated path to the directory containing view files. */
    private readonly Directory $viewsDirectory;
    
    /** The validated path to the cache directory for compiled templates. */
    private readonly Directory $cacheDirectory;

    /** The validated path to the directory containing static assets (CSS, JS, images). */
    private readonly Directory $assetsDirectory;

    /** The service responsible for resolving paths to templates, JS, CSS, etc. */
    private readonly PathResolvingServiceInterface $pathResolvingService;

    /** The high-level service for processing and compiling templates. */
    private readonly TemplateProcessingServiceInterface $templateProcessingService;

    /** The high-level service for building a complete Page object. */
    private readonly BuildingServiceInterface $buildingService;

    /** The high-level service for rendering a complete Page object into HTML. */
    private readonly RenderingServiceInterface $renderingService;

    /** The public-facing facade for the entire rendering module. */
    private RenderingFacadeInterface $renderer;

    /** The name of the copyright holder. */
    private readonly string $copyrightOwner;

    /** The copyright message text. */
    private readonly string $copyrightMessage;

    /** Whether to enable debug mode. */
    private readonly bool $debugMode;

    /**
     * Initializes the kernel and boots all subsystems in the correct order.
     *
     * @param RenderingConfigInterface $config A data object containing all necessary settings.
     * @param bool $debugMode Whether to enable debug mode for template processing.
     */
    public function __construct(RenderingConfigInterface $config, bool $debugMode = false)
    {
        $this->debugMode = $debugMode;
        $this->initiateConfig($config);
        $this->bootComponents();
    }

    /**
     * Returns the ready-to-use rendering facade, the main public entry point for the module.
     *
     * @return RenderingFacadeInterface
     */
    public function renderer(): RenderingFacadeInterface
    {
        return $this->renderer;
    }

    /**
     * Loads configuration from the config object and instantiates core value objects.
     */
    private function initiateConfig(RenderingConfigInterface $config): void
    {
        $this->viewsDirectory = new Directory(
            $config->viewsDirectory()
        );
        $this->cacheDirectory = new Directory(
            $config->cacheDirectory()
        );
        $this->assetsDirectory = new Directory(
            $config->assetsDirectory()
        );
        $this->copyrightOwner = $config->copyrightOwner();
        $this->copyrightMessage = $config->copyrightMessage();
    }

    /**
     * Acts as the main bootstrap coordinator for all service subsystems.
     */
    private function bootComponents(): void
    {
        $this->bootPathResolving();
        $this->bootTemplateProcessing();
        $this->bootPageBuilding();
        $this->bootRenderingEngine();
        $this->createRenderingFacade();
    }

    private function bootPathResolving(): void
    {
        $this->pathResolvingService = PathResolvingFactory::create([
            'template' => $this->viewsDirectory,
            'resource' => $this->assetsDirectory
        ]);
    }

    /**
     * Instantiates and wires the entire template processing subsystem.
     */
    private function bootTemplateProcessing(): void
    {
        $this->templateProcessingService = ProcessingServiceFactory::create(
            $this->cacheDirectory,
            $this->pathResolvingService,
            $this->debugMode
        );
    }

    /**
     * Instantiates and wires the entire page building subsystem.
     */
    private function bootPageBuilding(): void
    {
        $this->buildingService = BuildingServiceFactory::create(
            $this->pathResolvingService,
            $this->copyrightOwner,
            $this->copyrightMessage
        );
    }

    /**
     * Instantiates and wires the rendering engine subsystem.
     */
    private function bootRenderingEngine(): void
    {
        $this->renderingService = RenderingServiceFactory::create(
            $this->templateProcessingService,
            $this->debugMode
        );
    }

    /**
     * Instantiates the final public-facing facade with its service dependencies.
     */
    private function createRenderingFacade(): void
    {
        $this->renderer = new RenderingFacade(
            $this->buildingService,
            $this->renderingService
        );
    }
}