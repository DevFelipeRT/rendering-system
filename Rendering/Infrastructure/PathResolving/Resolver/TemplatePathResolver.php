<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver;

use Rendering\Domain\Trait\Validation\TemplateFileValidationTrait;
use RuntimeException;
use Rendering\Domain\ValueObject\Shared\Directory;

/**
 * Resolves a template name into an absolute, validated file path using a guaranteed-valid base directory.
 */
final class TemplatePathResolver extends AbstractPathResolver
{
    use TemplateFileValidationTrait;

    /**
     * @param Directory $viewsDirectory A value object representing the validated base views directory.
     */
    public function __construct(Directory $viewsDirectory)
    {
        parent::__construct($viewsDirectory);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $templateName): string
    {
        if ($this->isAlreadyResolvedPath($templateName)) {
            return $this->validateAndReturnResolvedPath($templateName);
        }

        $this->validateInputPath($templateName);

        // Add .phtml extension if not present
        if (!str_ends_with($templateName, '.phtml')) {
            $templateName .= '.phtml';
        }
        
        $normalizedTemplateName = $this->normalizePath($templateName);
        $potentialPath = $this->combinePaths($this->basePath, $normalizedTemplateName);
        return $this->validateAndReturnResolvedPath($potentialPath);
    }

    /**
     * Validates the template name before processing.
     *
     * @param string $templateName
     * @throws RuntimeException If the template name is invalid
     */
    protected function validateInputPath(string $templateName): void
    {
        self::validateTemplateFile($templateName);
    }
}
