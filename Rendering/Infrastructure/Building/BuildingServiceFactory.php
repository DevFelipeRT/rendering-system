<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building;

use Rendering\Domain\Contract\Service\Building\BuildingServiceInterface;
use Rendering\Infrastructure\Building\Renderable\Page\AssetsFactory;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\BuilderFactory;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\NavigationLinkFactory;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Building\Renderable\RenderableDataFactory;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\Building\Renderable\Partial\PartialBuildingServiceFactory;
use Rendering\Infrastructure\Building\Renderable\Page\PageBuildingService;
use Rendering\Infrastructure\Building\Renderable\View\ViewBuildingService;

/**
 * Factory responsible for creating and wiring the complete building service
 * with all its dependencies.
 * 
 * This factory encapsulates the instantiation logic for the entire page building
 * subsystem, reducing complexity in the main kernel.
 */
final class BuildingServiceFactory
{
    /**
     * Creates a fully configured BuildingService with all its dependencies.
     *
     * @param PathResolvingServiceInterface $pathResolver The path resolving service for file paths.
     * @param string $copyrightOwner The name of the copyright holder.
     * @param string $copyrightMessage The copyright message text.
     * @return BuildingServiceInterface The configured building service.
     */
    public static function create(
        PathResolvingServiceInterface $pathResolver,
        string $copyrightOwner,
        string $copyrightMessage
    ): BuildingServiceInterface {
        $renderableDataFactory = new RenderableDataFactory();
        $partialFactory = new PartialFactory($renderableDataFactory);
        $assetsFactory = new AssetsFactory();
        $linksFactory = new NavigationLinkFactory();

        $builders = BuilderFactory::createAll(
            $renderableDataFactory,
            $partialFactory,
            $assetsFactory,
            $linksFactory,
            $copyrightOwner,
            $copyrightMessage
        );

        $partialBuildingService = PartialBuildingServiceFactory::create(
            $builders['headerBuilder'],
            $builders['footerBuilder'],
            $builders['navigationBuilder'],
            $builders['partialBuilder']
        );
        
        $pageBuildingService = new PageBuildingService(
            $builders['pageBuilder']
        );
        
        $viewBuildingService = new ViewBuildingService(
            $builders['viewBuilder']
        );
        
        return new BuildingService(
            $pageBuildingService,
            $viewBuildingService,
            $partialBuildingService,
            $pathResolver
        );
    }
}
