<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\PathResolving;

use Rendering\Infrastructure\Contract\PathResolving\Resolver\PathResolverInterface;

/**
 * Contract for a service that orchestrates multiple path resolvers (templates, resources, etc).
 */
interface PathResolvingServiceInterface
{
    /**
     * Register a resolver for a specific type (e.g., 'template', 'js', 'css').
     *
     * @param string $type
     * @param PathResolverInterface $resolver
     * @return void
     */
    public function registerResolver(string $type, PathResolverInterface $resolver): void;

    /**
     * Resolve a template name to its absolute file path.
     *
     * @param string $templateName
     * @return string Absolute, validated template file path
     * @throws \RuntimeException If template resolver is not registered or resolution fails
     */
    public function resolveTemplate(string $templateName): string;

    /**
     * Resolve a JavaScript file name to its absolute file path.
     *
     * @param string $fileName
     * @return string Absolute, validated JavaScript file path
     * @throws \RuntimeException If JavaScript resolver is not registered or resolution fails
     */
    public function resolveJavascript(string $fileName): string;

    /**
     * Resolve a CSS file name to its absolute file path.
     *
     * @param string $fileName
     * @return string Absolute, validated CSS file path
     * @throws \RuntimeException If CSS resolver is not registered or resolution fails
     */
    public function resolveCss(string $fileName): string;

    /**
     * Resolves a relative asset path into a full, web-accessible URL.
     *
     * This method should also verify that the corresponding physical file exists.
     *
     * @param string $relativePath The relative path of the asset (e.g., 'css/style.css').
     * @return string The web-accessible URL for the asset (e.g., '/resources/css/style.css').
     * @throws RuntimeException if the asset file is not found or is not readable.
     */
    public function resolveAsset(string $relativePath): string;

    /**
     * Resolve a file path based on its type and name.
     *
     * @param string $type The type of the file (e.g., 'template', 'js', 'css')
     * @param string $fileName The name of the file to resolve
     * @return string Absolute, validated file path
     * @throws \RuntimeException If no resolver is registered for the type or resolution fails
     */
    public function resolve(string $type, string $fileName): string;

    /**
     * Check if a resolver is registered for a given type.
     *
     * @param string $type
     * @return bool
     */
    public function hasResolver(string $type): bool;
}
