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
    private ?string $cachedBasePath = null;

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
     * Versão otimizada para produção.
     *
     * @return string The detected base web path
     */
    public function detectBasePath(): string
    {
        if ($this->cachedBasePath !== null) {
            return $this->cachedBasePath;
        }

        // Configuração específica para domínios conhecidos
        $host = $_SERVER['HTTP_HOST'] ?? '';
        
        // Se for um domínio específico na raiz, retorna vazio
        if (in_array($host, ['renderingsystem.devfelipert.com.br'])) {
            $this->cachedBasePath = '';
            return $this->cachedBasePath;
        }

        // Para desenvolvimento local
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            return $this->detectLocalBasePath();
        }

        // Método 1: Usar SCRIPT_NAME (mais confiável em produção)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
        if (!empty($scriptName)) {
            $pathParts = explode('/', trim($scriptName, '/'));
            
            // Remove o arquivo (index.php)
            if (!empty($pathParts) && str_contains(end($pathParts), '.php')) {
                array_pop($pathParts);
            }
            
            // Remove 'public' se for o último diretório
            if (!empty($pathParts) && end($pathParts) === 'public') {
                array_pop($pathParts);
            }
            
            if (!empty($pathParts)) {
                $this->cachedBasePath = '/' . implode('/', $pathParts);
                return $this->cachedBasePath;
            }
        }

        // Método 2: Usar REQUEST_URI
        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if ($requestUri && $requestUri !== '/') {
                $pathParts = explode('/', trim($requestUri, '/'));
                
                // Remove 'public' se estiver no final
                if (!empty($pathParts) && end($pathParts) === 'public') {
                    array_pop($pathParts);
                }
                
                // Se ainda temos partes, usar como base path
                if (!empty($pathParts)) {
                    // Para produção, muitas vezes é melhor assumir raiz
                    // a menos que claramente seja um subdiretório
                    if (count($pathParts) === 1 && !str_contains($pathParts[0], '.')) {
                        $this->cachedBasePath = '/' . $pathParts[0];
                        return $this->cachedBasePath;
                    }
                }
            }
        }

        // Fallback: assumir raiz
        $this->cachedBasePath = '';
        return $this->cachedBasePath;
    }

    /**
     * Detecção específica para ambiente local
     */
    private function detectLocalBasePath(): string
    {
        // Normalize paths for comparison
        $normalizedProjectRoot = str_replace('\\', '/', $this->projectRoot);
        $normalizedDocRoot = str_replace('\\', '/', $this->documentRoot);

        // Calculate relative path from document root to project root
        if (!empty($normalizedDocRoot) && str_starts_with($normalizedProjectRoot, $normalizedDocRoot)) {
            $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocRoot));
            $basePath = '/' . ltrim($relativePath, '/');
            
            // Se o path for apenas '/', significa que estamos na raiz
            if ($basePath === '/') {
                $basePath = '';
            }
            
            $this->cachedBasePath = $basePath;
            return $this->cachedBasePath;
        }

        // Fallback para local
        $this->cachedBasePath = '';
        return $this->cachedBasePath;
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
        
        // Se basePath estiver vazio, retorna apenas a baseUrl
        if (empty($basePath)) {
            return $baseUrl;
        }
        
        return rtrim($baseUrl, '/') . $basePath;
    }

    /**
     * Gets the web path for resources.
     *
     * @return string The web path for resources
     */
    public function getResourcesWebPath(): string
    {
        $basePath = $this->detectBasePath();
        return $basePath . '/resources';
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
            $basePath = $this->detectBasePath();
            
            return $basePath . '/' . ltrim($relativePath, '/');
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

    /**
     * Force a specific base path (useful for production configuration)
     *
     * @param string $basePath The base path to force
     */
    public function forceBasePath(string $basePath): void
    {
        $this->cachedBasePath = $basePath;
    }
}

// Classe UrlDetector atualizada para ser mais robusta
final class UrlDetector
{
    /**
     * Detects the full base URL, including scheme and host.
     *
     * @return string The detected base URL (e.g., "https://example.com").
     * @throws RuntimeException If the host name cannot be determined from the environment.
     */
    public function detectBaseUrl(): string
    {
        // Verifica múltiplos headers para o host
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        
        if (empty($host)) {
            throw new RuntimeException('Unable to determine HTTP host from server environment.');
        }

        // Detecta o esquema de forma mais robusta
        $scheme = 'http';
        
        // Verifica HTTPS de várias formas
        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        ) {
            $scheme = 'https';
        }

        return "{$scheme}://{$host}";
    }
}