<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Detector;

use RuntimeException;

/**
 * Utility class for detecting web paths dynamically.
 */
class WebPathDetector
{
    private readonly string $projectRoot;
    private readonly string $documentRoot;
    
    /**
     * @param string|null $projectRoot The absolute filesystem path to the project root
     */
    public function __construct(?string $projectRoot = null)
    {
        $this->projectRoot = $projectRoot ?? dirname(__DIR__, 4);
        $this->documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        
        if (empty($this->documentRoot)) {
            throw new RuntimeException('Unable to determine document root from server environment');
        }
    }

    /**
     * Detects the base web path for the application.
     *
     * @return string The detected base web path
     * @throws RuntimeException If the base path cannot be detected
     */
    public function detectBasePath(): string
    {
        // Normalize paths for comparison
        $normalizedProjectRoot = str_replace('\\', '/', $this->projectRoot);
        $normalizedDocRoot = str_replace('\\', '/', $this->documentRoot);
        
        // Calculate the relative path from document root to project root
        if (str_starts_with($normalizedProjectRoot, $normalizedDocRoot)) {
            $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocRoot));
            return '/' . ltrim($relativePath, '/');
        }
        
        // Use alternative detection methods
        $scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
        
        if (!empty($scriptPath) && !empty($scriptName) && str_ends_with($scriptPath, $scriptName)) {
            $basePath = substr($scriptPath, 0, -strlen($scriptName));
            if (str_starts_with($normalizedProjectRoot, $basePath)) {
                $relativePath = substr($normalizedProjectRoot, strlen($basePath));
                return '/' . ltrim($relativePath, '/');
            }
        }
        
        throw new RuntimeException('Unable to detect base web path for the application');
    }
    
    /**
     * Gets the web path for resources.
     *
     * @return string The web path for resources
     */
    public function getResourcesWebPath(): string
    {
        return $this->detectBasePath() . '/resources';
    }
    
    /**
     * Converts a file system path to a web-accessible URL.
     *
     * @param string $absolutePath The absolute file system path
     * @return string The web-accessible URL
     * @throws RuntimeException If the path cannot be converted
     */
    public function convertToWebPath(string $absolutePath): string
    {
        $normalizedPath = str_replace('\\', '/', $absolutePath);
        $normalizedRoot = str_replace('\\', '/', $this->projectRoot);
        
        if (str_starts_with($normalizedPath, $normalizedRoot)) {
            $relativePath = substr($normalizedPath, strlen($normalizedRoot));
            return $this->detectBasePath() . '/' . ltrim($relativePath, '/');
        }
        
        throw new RuntimeException("Cannot convert path to web path: {$absolutePath}");
    }
}
