<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Detector;

use RuntimeException;

/**
 * Detects the application's base URL from the server environment.
 */
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
        if (empty($_SERVER['HTTP_HOST'])) {
            throw new RuntimeException('Unable to determine HTTP host from server environment.');
        }

        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];

        return "{$scheme}://{$host}";
    }
}
