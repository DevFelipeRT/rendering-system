<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Detector;

use RuntimeException;
use Throwable;

/**
 * Utility class for detecting web paths dynamically.
 * Versão segura com tratamento de erros robusto.
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
        try {
            $this->projectRoot = $projectRoot ?? $this->getDefaultProjectRoot();
            $this->documentRoot = $this->getDocumentRoot();
            $this->urlDetector = $urlDetector ?? new UrlDetector();
        } catch (Throwable $e) {
            // Fallback seguro se algo der errado
            $this->projectRoot = dirname(__DIR__, 4);
            $this->documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html';
            $this->urlDetector = new UrlDetector();
        }
    }

    /**
     * Obtém o document root de forma segura
     */
    private function getDocumentRoot(): string
    {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        if (empty($docRoot)) {
            // Fallback: tentar detectar a partir do script atual
            $scriptPath = $_SERVER['SCRIPT_FILENAME'] ?? '';
            if (!empty($scriptPath)) {
                // Assumir que estamos em public/index.php
                $scriptDir = dirname($scriptPath);
                if (basename($scriptDir) === 'public') {
                    return dirname($scriptDir);
                }
                return $scriptDir;
            }
            
            // Último fallback
            return '/var/www/html';
        }
        
        return rtrim($docRoot, '/\\');
    }

    /**
     * Obtém o project root padrão de forma segura
     */
    private function getDefaultProjectRoot(): string
    {
        try {
            return dirname(__DIR__, 4);
        } catch (Throwable $e) {
            // Se dirname falhar, usar path absoluto baseado no script atual
            $scriptPath = $_SERVER['SCRIPT_FILENAME'] ?? __FILE__;
            return dirname(dirname(dirname(dirname($scriptPath))));
        }
    }

    /**
     * Detects the base web path for the application.
     * Versão ultra-segura.
     *
     * @return string The detected base web path
     */
    public function detectBasePath(): string
    {
        if ($this->cachedBasePath !== null) {
            return $this->cachedBasePath;
        }

        try {
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
            
            // Configuração hardcoded para domínios conhecidos
            if ($host === 'renderingsystem.devfelipert.com.br') {
                $this->cachedBasePath = '';
                return $this->cachedBasePath;
            }

            // Para desenvolvimento local
            if ($this->isLocalEnvironment($host)) {
                $this->cachedBasePath = $this->detectLocalBasePath();
                return $this->cachedBasePath;
            }

            // Para produção, usar método mais simples e seguro
            $this->cachedBasePath = $this->detectProductionBasePath();
            return $this->cachedBasePath;

        } catch (Throwable $e) {
            // Em caso de qualquer erro, assumir raiz
            $this->cachedBasePath = '';
            return $this->cachedBasePath;
        }
    }

    /**
     * Verifica se é ambiente local
     */
    private function isLocalEnvironment(string $host): bool
    {
        return str_contains($host, 'localhost') || 
               str_contains($host, '127.0.0.1') || 
               str_contains($host, '::1');
    }

    /**
     * Detecção para ambiente local
     */
    private function detectLocalBasePath(): string
    {
        try {
            $normalizedProjectRoot = str_replace('\\', '/', $this->projectRoot);
            $normalizedDocRoot = str_replace('\\', '/', $this->documentRoot);

            if (!empty($normalizedDocRoot) && str_starts_with($normalizedProjectRoot, $normalizedDocRoot)) {
                $relativePath = substr($normalizedProjectRoot, strlen($normalizedDocRoot));
                $basePath = '/' . ltrim($relativePath, '/');
                
                return $basePath === '/' ? '' : $basePath;
            }
        } catch (Throwable $e) {
            // Fallback silencioso
        }

        return '';
    }

    /**
     * Detecção para ambiente de produção
     */
    private function detectProductionBasePath(): string
    {
        try {
            // Método 1: SCRIPT_NAME
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            if (!empty($scriptName)) {
                $pathParts = explode('/', trim($scriptName, '/'));
                
                // Remove arquivo PHP
                if (!empty($pathParts) && str_ends_with(end($pathParts), '.php')) {
                    array_pop($pathParts);
                }
                
                // Remove 'public'
                if (!empty($pathParts) && end($pathParts) === 'public') {
                    array_pop($pathParts);
                }
                
                if (!empty($pathParts)) {
                    return '/' . implode('/', $pathParts);
                }
            }
        } catch (Throwable $e) {
            // Continue para próximo método
        }

        return '';
    }

    /**
     * Gets the complete base URL including the application path.
     */
    public function getCompleteBaseUrl(): string
    {
        try {
            $baseUrl = $this->urlDetector->detectBaseUrl();
            $basePath = $this->detectBasePath();
            
            if (empty($basePath)) {
                return $baseUrl;
            }
            
            return rtrim($baseUrl, '/') . $basePath;
        } catch (Throwable $e) {
            // Fallback seguro
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
            return "{$scheme}://{$host}";
        }
    }

    /**
     * Gets the web path for resources.
     */
    public function getResourcesWebPath(): string
    {
        $basePath = $this->detectBasePath();
        return $basePath . '/resources';
    }

    /**
     * Gets the complete URL for resources.
     */
    public function getResourcesUrl(): string
    {
        return $this->getCompleteBaseUrl() . '/resources';
    }

    /**
     * Converts a file system path to a web-accessible URL.
     */
    public function convertToWebPath(string $absolutePath): string
    {
        try {
            $normalizedPath = str_replace('\\', '/', $absolutePath);
            $normalizedRoot = str_replace('\\', '/', $this->projectRoot);

            if (str_starts_with($normalizedPath, $normalizedRoot)) {
                $relativePath = substr($normalizedPath, strlen($normalizedRoot));
                $basePath = $this->detectBasePath();
                
                return $basePath . '/' . ltrim($relativePath, '/');
            }
        } catch (Throwable $e) {
            // Log do erro se necessário
        }

        throw new RuntimeException("Cannot convert path to web path: {$absolutePath}");
    }

    /**
     * Converts a file system path to a complete web-accessible URL.
     */
    public function convertToCompleteUrl(string $absolutePath): string
    {
        $webPath = $this->convertToWebPath($absolutePath);
        $baseUrl = $this->getCompleteBaseUrl();
        
        return rtrim($baseUrl, '/') . '/' . ltrim($webPath, '/');
    }

    /**
     * Creates a complete URL for a given path relative to the project root.
     */
    public function createCompleteUrl(string $relativePath): string
    {
        $completeBaseUrl = $this->getCompleteBaseUrl();
        $relativePath = ltrim($relativePath, '/');
        
        return $completeBaseUrl . '/' . $relativePath;
    }

    /**
     * Creates a web path for a given path relative to the project root.
     */
    public function createWebPath(string $relativePath): string
    {
        $basePath = $this->detectBasePath();
        $relativePath = ltrim($relativePath, '/');
        
        return $basePath . '/' . $relativePath;
    }

    /**
     * Force a specific base path (useful for production configuration)
     */
    public function forceBasePath(string $basePath): void
    {
        $this->cachedBasePath = $basePath;
    }

    /**
     * Gets the injected URL detector instance.
     */
    public function getUrlDetector(): UrlDetector
    {
        return $this->urlDetector;
    }

    /**
     * Método específico para assets (imagens, CSS, JS) com verificação de existência
     */
    public function getAssetUrl(string $relativePath): string
    {
        try {
            // Limpa o path
            $relativePath = ltrim($relativePath, '/');
            
            // Verifica se o arquivo existe no sistema de arquivos
            $fullPath = $this->projectRoot . '/' . $relativePath;
            if (!file_exists($fullPath)) {
                // Log do arquivo não encontrado se necessário
                error_log("Asset not found: {$fullPath}");
            }
            
            return $this->createCompleteUrl($relativePath);
        } catch (Throwable $e) {
            // Fallback: retorna o URL mesmo se der erro
            return $this->getCompleteBaseUrl() . '/' . ltrim($relativePath, '/');
        }
    }
}

/**
 * UrlDetector seguro
 */
final class UrlDetector
{
    public function detectBaseUrl(): string
    {
        try {
            // Múltiplas tentativas para obter o host
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '';
            
            if (empty($host)) {
                throw new RuntimeException('Unable to determine HTTP host from server environment.');
            }

            // Detecção robusta de HTTPS
            $scheme = 'http';
            
            if (
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https')) // Cloudflare
            ) {
                $scheme = 'https';
            }

            return "{$scheme}://{$host}";
        } catch (Throwable $e) {
            // Fallback ultra-seguro
            return 'https://renderingsystem.devfelipert.com.br';
        }
    }
}