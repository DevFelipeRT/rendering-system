<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver;

use Rendering\Infrastructure\Contract\PathResolving\Resolver\PathResolverInterface;
use RuntimeException;
use Rendering\Domain\ValueObject\Shared\Directory;

/**
 * Abstract implementation of PathResolverInterface that provides common
 * functionality for resolving paths using a base directory.
 */
abstract class AbstractPathResolver implements PathResolverInterface
{
    protected readonly string $basePath;

    /**
     * @param Directory $baseDirectory A value object representing the validated base directory
     */
    public function __construct(protected readonly Directory $baseDirectory) 
    {
        $this->basePath = $this->baseDirectory->path();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $path): string
    {
        // If the path is already resolved, validate and return directly
        if ($this->isAlreadyResolvedPath($path)) {
            return $this->validateAndReturnResolvedPath($path);
        }

        // If the path is absolute, validate and return directly
        if ($this->isAbsolutePath($path)) {
            return $this->validateAndReturnResolvedPath($path);
        }

        $this->validateInputPath($path);

        $normalizedPath = $this->normalizePath($path);
        $potentialPath = $this->combinePaths($this->basePath, $normalizedPath);

        return $this->validateAndReturnResolvedPath($potentialPath);
    }

    /**
     * Checks if the given path is absolute (Windows or Unix).
     *
     * @param string $path
     * @return bool
     */
    protected function isAbsolutePath(string $path): bool
    {
        // Windows absolute path: C:\...
        if (preg_match('/^[A-Za-z]:[\\/]/', $path) === 1) {
            return true;
        }
        // Unix absolute path: /...
        return str_starts_with($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Checks if the given path is already resolved, exists, and is within the allowed base directory.
     *
     * @param string $path The path to check
     * @return bool True if the path is already resolved and valid
     */
    protected function isAlreadyResolvedPath(string $path): bool
    {
        return is_string($path)
            && file_exists($path)
            && str_starts_with($path, $this->basePath);
    }

    /**
     * Validates the input path before processing.
     *
     * @param string $path The path to validate
     * @throws RuntimeException If the path is invalid
     */
    abstract protected function validateInputPath(string $path): void;

    /**
     * Normalizes the input path for system compatibility.
     *
     * @param string $path The path to normalize
     * @return string The normalized path
     */
    protected function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Combines base path with the normalized path.
     *
     * @param string $basePath The base path
     * @param string $normalizedPath The normalized path to append
     * @return string The combined path
     */
    protected function combinePaths(string $basePath, string $normalizedPath): string
    {
        return $basePath . DIRECTORY_SEPARATOR . ltrim($normalizedPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Validates the resolved path and returns the absolute path.
     *
     * @param string $potentialPath The potential path to validate
     * @return string The validated absolute path
     * @throws RuntimeException If the path doesn't exist or is outside allowed directory
     */
    protected function validateAndReturnResolvedPath(string $potentialPath): string
    {
        $realPath = realpath($potentialPath);

        if ($realPath === false) {
            throw new RuntimeException("File not found or is not accessible: {$potentialPath}");
        }

        if (!str_starts_with($realPath, $this->basePath)) {
            throw new RuntimeException("Resolved path is outside of the allowed directory.");
        }

        return $realPath;
    }
}