<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving;

use InvalidArgumentException;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\PathResolving\Resolver\Resource\AssetPathResolver;
use Rendering\Infrastructure\PathResolving\Resolver\Resource\CssPathResolver;
use Rendering\Infrastructure\PathResolving\Resolver\Resource\JavascriptPathResolver;
use Rendering\Infrastructure\PathResolving\Resolver\TemplatePathResolver;

/**
 * Factory responsible for creating and configuring the PathResolvingService
 * with all available resolvers (template, js, css, etc).
 */
final class PathResolvingFactory
{
    /**
     * Defines the mapping between directory identifiers and the resolvers they serve.
     *
     * The outer key is the directory identifier (e.g., 'template', 'resource').
     * The inner array maps a resolver type to its corresponding class.
     */
    private const RESOLVERS_MAP = [
        'template' => [
            'template' => TemplatePathResolver::class,
        ],
        'resource' => [
            'js' => JavascriptPathResolver::class,
            'css' => CssPathResolver::class,
            'asset' => AssetPathResolver::class,
        ],
    ];

    /**
     * Creates and configures a PathResolvingService instance.
     *
     * @param array<string, object> $directories An associative array mapping a directory
     * identifier to its corresponding directory object.
     * Example: [
     * 'template' => $templateDirectory,
     * 'resource' => $resourceDirectory,
     * ]
     * @return PathResolvingServiceInterface The fully configured service.
     * @throws InvalidArgumentException If a required directory is missing.
     */
    public static function create(array $directories): PathResolvingServiceInterface
    {
        $service = new PathResolvingService();
        self::registerResolvers($service, $directories);
        return $service;
    }

    /**
     * Dynamically registers resolvers based on the provided directories and the map.
     *
     * @param PathResolvingService $service The service to register resolvers on.
     * @param array<string, object> $directories The available directory objects.
     */
    private static function registerResolvers(PathResolvingService $service, array $directories): void
    {
        foreach (self::RESOLVERS_MAP as $directoryKey => $resolvers) {
            // Check if the required directory for this group of resolvers was provided.
            if (!isset($directories[$directoryKey])) {
                // Throw an exception if a directory expected by the map is not provided.
                throw new InvalidArgumentException("Missing required directory key: '{$directoryKey}'.");
            }

            $directoryObject = $directories[$directoryKey];

            // Instantiate and register each resolver associated with this directory.
            foreach ($resolvers as $type => $resolverClass) {
                $resolver = new $resolverClass($directoryObject);
                $service->registerResolver($type, $resolver);
            }
        }
    }
}
