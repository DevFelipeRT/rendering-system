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
    private readonly UrlDetector $urlDetector;

    /**
     * @param string|null $projectRoot The absolute filesystem path to the project root
     * @param UrlDetector|null $urlDetector URL detector dependency
     */
    public function __construct(?string $projectRoot = null, ?UrlDetector $urlDetector = null)
    {
        $this->projectRoot = $projectRoot ?? dirname(__DIR__, 4);
        $this->documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        $this->urlDetector = $urlDetector ?? new UrlDetector();

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
     * Gets the complete base URL including the application path.
     *
     * @return string The complete base URL (e.g., "https://example.com/myapp")
     * @throws RuntimeException If the base URL or path cannot be detected
     */
    public function getCompleteBaseUrl(): string
    {
        $baseUrl = $this->urlDetector->detectBaseUrl();
        $basePath = $this->detectBasePath();
        
        return rtrim($baseUrl, '/') . $basePath;
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
     * Gets the complete URL for resources.
     *
     * @return string The complete URL for resources (e.g., "https://example.com/myapp/resources")
     * @throws RuntimeException If the base URL cannot be detected
     */
    public function getResourcesUrl(): string
    {
        return $this->getCompleteBaseUrl() . '/resources';
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

    /**
     * Converts a file system path to a complete web-accessible URL.
     *
     * @param string $absolutePath The absolute file system path
     * @return string The complete web-accessible URL
     * @throws RuntimeException If the path cannot be converted or base URL cannot be detected
     */
    public function convertToCompleteUrl(string $absolutePath): string
    {
        $webPath = $this->convertToWebPath($absolutePath);
        $baseUrl = $this->urlDetector->detectBaseUrl();
        
        return $baseUrl . $webPath;
    }

    /**
     * Creates a complete URL for a given path relative to the project root.
     *
     * @param string $relativePath Path relative to project root (e.g., 'resources/css/style.css')
     * @return string Complete URL
     * @throws RuntimeException If the base URL or path cannot be detected
     */
    public function createCompleteUrl(string $relativePath): string
    {
        $completeBaseUrl = $this->getCompleteBaseUrl();
        $relativePath = ltrim($relativePath, '/');
        
        return $completeBaseUrl . '/' . $relativePath;
    }

    /**
     * Creates a web path for a given path relative to the project root.
     *
     * @param string $relativePath Path relative to project root (e.g., 'resources/css/style.css')
     * @return string Web path
     */
    public function createWebPath(string $relativePath): string
    {
        $basePath = $this->detectBasePath();
        $relativePath = ltrim($relativePath, '/');
        
        return $basePath . '/' . $relativePath;
    }

    /**
     * Gets the injected URL detector instance.
     *
     * @return UrlDetector The URL detector dependency
     */
    public function getUrlDetector(): UrlDetector
    {
        return $this->urlDetector;
    }
}