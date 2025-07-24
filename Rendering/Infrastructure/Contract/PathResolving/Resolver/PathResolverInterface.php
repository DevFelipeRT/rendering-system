<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\PathResolving\Resolver;

/**
 * Contract for resolving a file name into its absolute, validated file system path.
 *
 * Implementations must ensure the returned path is absolute, exists, and is within the allowed base directory.
 */
interface PathResolverInterface
{
    /**
     * Resolves a file name into its absolute, validated file system path.
     *
     * @param string $fileName The file name to resolve (may include relative path segments)
     * @return string The resolved absolute file path
     * @throws RuntimeException If the file cannot be found, accessed, or is outside the allowed directory
     */
    public function resolve(string $fileName): string;
}