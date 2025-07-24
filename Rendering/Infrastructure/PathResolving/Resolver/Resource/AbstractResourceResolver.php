<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver\Resource;

use Rendering\Domain\ValueObject\Shared\Directory;
use Rendering\Infrastructure\Contract\PathResolving\Resolver\ResourceResolverInterface;
use Rendering\Infrastructure\PathResolving\Resolver\AbstractPathResolver;
use RuntimeException;
use Rendering\Infrastructure\PathResolving\Detector\WebPathDetector;

/**
 * Abstract resource resolver that converts file system paths to web-accessible URLs.
 * 
 * This class provides a unified approach for resolving resource files (CSS, JS, etc.)
 * with automatic extension handling, subdirectory organization, and web path conversion.
 */
abstract class AbstractResourceResolver extends AbstractPathResolver implements ResourceResolverInterface
{
    protected const SUBDIRECTORY = '';
    protected const FILE_EXTENSION = '';
    
    protected readonly string $baseWebPath;
    protected readonly WebPathDetector $webPathDetector;

    /**
     * @param Directory $resourceDirectory A value object representing the resources directory
     * @param WebPathDetector|null $webPathDetector The web path detector (optional)
     */
    public function __construct(
        Directory $resourceDirectory,
        ?WebPathDetector $webPathDetector = null
    ) {
        parent::__construct($resourceDirectory);
        $this->webPathDetector = $webPathDetector ?? new WebPathDetector();
        $this->baseWebPath = $this->webPathDetector->getResourcesWebPath();
    }

    /**
     * Converts a file system path to a web-accessible URL.
     *
     * @param string $absolutePath The absolute file system path
     * @return string The web-accessible URL
     */
    public function convertToWebPath(string $absolutePath): string
    {
        return $this->webPathDetector->convertToWebPath($absolutePath);
    }

    /**
     * Resolves a relative path to an absolute path and then converts it to a web URL.
     *
     * @param string $path The relative path to resolve
     * @return string The web-accessible URL
     */
    public function resolveToWebPath(string $path): string
    {
        var_dump($path);
        $absolutePath = $this->resolve($path);
        return $this->convertToWebPath($absolutePath);
    }

    /**
     * {@inheritdoc}
     * 
     * Provides default implementation for resource resolution with extension handling.
     */
    public function resolve(string $fileName): string
    {
        // If the path is already resolved, validate and return directly
        if ($this->isAlreadyResolvedPath($fileName)) {
            return $this->validateAndReturnResolvedPath($fileName);
        }

        $this->validateInputPath($fileName);

        // Add file extension if not present
        if (!str_ends_with($fileName, static::FILE_EXTENSION)) {
            $fileName .= static::FILE_EXTENSION;
        }

        $normalizedFileName = $this->normalizePath($fileName);
        // Combine base resources path with resource-specific subdirectory
        $resourcePath = $this->combinePaths($this->basePath, static::SUBDIRECTORY);
        $potentialPath = $this->combinePaths($resourcePath, $normalizedFileName);
        
        $validated = $this->validateAndReturnResolvedPath($potentialPath);

        return $validated;
    }

    /**
     * Validates the resource file name before processing.
     * 
     * Provides default validation - subclasses can override for specific requirements.
     *
     * @param string $fileName
     * @throws RuntimeException If the file name is invalid
     */
    protected function validateInputPath(string $fileName): void
    {
        // Basic validation: no directory traversal, no empty name, no invalid chars
        if (empty($fileName) || str_contains($fileName, '..') || preg_match('/[^a-zA-Z0-9_\-\/\.]/', $fileName)) {
            throw new RuntimeException("Invalid " . static::SUBDIRECTORY . " file name: {$fileName}");
        }
    }
}