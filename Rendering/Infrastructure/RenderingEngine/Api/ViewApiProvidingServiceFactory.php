<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

use Rendering\Infrastructure\Contract\RenderingEngine\Api\ViewApiProvidingServiceInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuildingServiceInterface;

/**
 * Factory responsible for creating a fully configured ViewApiProvidingService.
 *
 * This class encapsulates the logic of assembling the dispatch map for ViewApi
 * types and wiring the provider with its required ContextBuildingService dependency.
 */
final class ViewApiProvidingServiceFactory
{
    /**
     * A map that associates a rendering stage with a concrete ViewApi implementation.
     */
    private const API_MAP = [
        'POPULATE' => PopulatingViewApi::class,
        'PRESENT' => PresentingViewApi::class,
    ];

    /**
     * Creates a new instance of ViewApiProvidingServiceInterface with all dependencies.
     *
     * @param ContextBuildingServiceInterface $contextBuildingService The service that builds contexts for ViewApi.
     * @return ViewApiProvidingServiceInterface The fully configured service.
     */
    public static function create(
        ContextBuildingServiceInterface $contextBuildingService
    ): ViewApiProvidingServiceInterface
    {
        return new ViewApiProvidingService(
            $contextBuildingService,
            self::API_MAP
        );
    }
}