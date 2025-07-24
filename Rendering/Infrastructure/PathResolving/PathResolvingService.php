<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving;

use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\Contract\PathResolving\Resolver\PathResolverInterface;
use RuntimeException;

/**
 * General service for orchestrating all path resolvers (templates, resources, etc).
 * Allows registration and resolution for any type of resolver.
 */
final class PathResolvingService implements PathResolvingServiceInterface
{
    /**
     * @var array<string, PathResolverInterface> Map of resolver type to resolver instance
     */
    private array $resolvers = [];

    /**
     * {@inheritdoc}
     */
    public function registerResolver(string $type, PathResolverInterface $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTemplate(string $templateName): string
    {
        return $this->resolveByType('template', $templateName);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveJavascript(string $fileName): string
    {
        return $this->resolveByType('js', $fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCss(string $fileName): string
    {
        return $this->resolveByType('css', $fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveAsset(string $relativePath): string
    {
        return $this->resolveByType('asset', $relativePath);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $type, string $name): string
    {
        return $this->resolveByType($type, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasResolver(string $type): bool
    {
        return isset($this->resolvers[$type]);
    }

    /**
     * Internal method to resolve by type.
     *
     * @param string $type
     * @param string $name
     * @return string
     * @throws RuntimeException
     */
    private function resolveByType(string $type, string $name): string
    {
        if (!isset($this->resolvers[$type])) {
            throw new RuntimeException("No resolver registered for type: {$type}");
        }
        return $this->resolvers[$type]->resolve($name);
    }
}
