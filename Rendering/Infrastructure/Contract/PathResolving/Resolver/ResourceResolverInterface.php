<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\PathResolving\Resolver;

/**
 * Contract for resource resolvers that can convert file system paths to web URLs.
 */
interface ResourceResolverInterface extends PathResolverInterface
{
    /**
     * Converts a file system path to a web-accessible URL.
     *
     * @param string $absolutePath The absolute file system path
     * @return string The web-accessible URL
     */
    public function convertToWebPath(string $absolutePath): string;

    /**
     * Resolves a relative path to an absolute path and then converts it to a web URL.
     *
     * @param string $path The relative path to resolve
     * @return string The web-accessible URL
     */
    public function resolveToWebPath(string $path): string;
}
