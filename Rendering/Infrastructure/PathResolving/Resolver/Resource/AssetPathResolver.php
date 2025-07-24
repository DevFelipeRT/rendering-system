<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver\Resource;

use Rendering\Infrastructure\Contract\PathResolving\Resolver\AssetPathResolverInterface;
use RuntimeException;

/**
 * Resolves generic asset identifiers (local filenames for images, fonts, etc.)
 * into fully qualified, web-accessible URLs. It also handles full external URLs.
 */
final class AssetPathResolver extends AbstractResourceResolver implements AssetPathResolverInterface
{
    /**
     * A map of file extensions to their corresponding subdirectories within the resources path.
     * This allows for organizing assets into folders like 'images', 'fonts', etc.
     */
    private const ASSET_SUBDIRECTORIES = [
        // Scripts
        'js'   => 'js',
        // Stylesheets
        'css'  => 'css',
        // Images
        'png'  => 'images',
        'jpg'  => 'images',
        'jpeg' => 'images',
        'gif'  => 'images',
        'svg'  => 'images',
        'webp' => 'images',
        'ico'  => 'images',
        // Fonts
        'woff'  => 'fonts',
        'woff2' => 'fonts',
        'ttf'   => 'fonts',
        'eot'   => 'fonts',
    ];

    /**
     * {@inheritdoc}
     *
     * This implementation handles various asset types by dynamically determining
     * the correct subdirectory based on the file extension. It also passes
     * through full URLs untouched.
     *
     * @param string $assetIdentifier The filename of a local asset (e.g., 'logo.svg') or a full external URL.
     */
    public function resolve(string $assetIdentifier): string
    {
        // If the identifier is already a full URL, return it directly.
        if ($this->isUrl($assetIdentifier)) {
            return $assetIdentifier;
        }
        // If the path is already resolved, validate and convert to web path
        if ($this->isAlreadyResolvedPath($assetIdentifier)) {
            $validatedPath = $this->validateAndReturnResolvedPath($assetIdentifier);
            return $this->convertToWebPath($validatedPath);
        }

        // 1. Perform initial validation on the input path.
        $this->validateInputPath($assetIdentifier);

        // 2. Determine the correct subdirectory (e.g., 'images', 'fonts') for the asset.
        $subdirectory = $this->getSubdirectoryForAsset($assetIdentifier);

        // 3. Normalize the path for system compatibility (reusing parent method).
        $normalizedFileName = $this->normalizePath($assetIdentifier);

        // 4. Combine the base path with the dynamic subdirectory and filename (reusing parent method).
        $resourcePath = $this->combinePaths($this->basePath, $subdirectory);
        $potentialPath = $this->combinePaths($resourcePath, $normalizedFileName);

        // 5. Validate the final path exists and is within the allowed directory (reusing parent method).
        $absolutePath = $this->validateAndReturnResolvedPath($potentialPath);

        // 6. Convert the absolute filesystem path to a web-accessible URL (reusing parent method).
        return $this->convertToWebPath($absolutePath);
    }

    /**
     * Determines the correct subdirectory for a given asset file based on its extension.
     *
     * @param string $fileName The name of the asset file.
     * @return string The corresponding subdirectory name.
     * @throws RuntimeException If the file has no extension or the extension is unsupported.
     */
    private function getSubdirectoryForAsset(string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (empty($extension)) {
            throw new RuntimeException("Cannot determine asset type: file '{$fileName}' has no extension.");
        }

        if (!isset(self::ASSET_SUBDIRECTORIES[$extension])) {
            throw new RuntimeException("Unsupported asset type (extension): '{$extension}'.");
        }

        return self::ASSET_SUBDIRECTORIES[$extension];
    }

    /**
     * Checks if the provided identifier is a full external URL.
     *
     * @param string $assetIdentifier The identifier to check.
     * @return bool True if it is a URL, false otherwise.
     */
    private function isUrl(string $assetIdentifier): bool
    {
        return str_starts_with($assetIdentifier, 'http://') ||
               str_starts_with($assetIdentifier, 'https://');
    }

    /**
     * {@inheritdoc}
     *
     * Overrides the parent validation to be more permissive for general asset files,
     * while still preventing directory traversal.
     */
    protected function validateInputPath(string $fileName): void
    {
        if (empty(trim($fileName))) {
            throw new RuntimeException("Asset identifier cannot be empty.");
        }
        if (str_contains($fileName, '..')) {
            throw new RuntimeException("Directory traversal is not permitted in asset identifier: {$fileName}");
        }
    }
}
