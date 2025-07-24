<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Asset;

use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * Compiler for CSS-related directives in templates.
 * 
 * Handles directives like:
 * - @css('fileName') - includes a CSS file
 * - @css('fileName', 'media') - includes a CSS file with media attribute
 */
final class CssCompiler extends AbstractDirectiveCompiler
{
    /**
     * Directives that require parameters in parentheses.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'css' => '/\@css\s*\(/',
    ];

    /**
     * A template for the output of CSS directives.
     */
    protected const OUTPUT_TEMPLATE = '<link rel="stylesheet" type="text/css" href="%s" media="%s">';

    /**
     * @param PathResolvingServiceInterface $pathResolvingService Service to resolve CSS file paths
     * 
     */
    public function __construct(
        private readonly PathResolvingServiceInterface $pathResolvingService,
        bool $debug
    ) {
        parent::__construct($debug);
    }

    /**
     * Builds the replacement for CSS directives.
     *
     * @param string $name The directive name ('css')
     * @param string $expression The expression within parentheses
     * @return string The compiled HTML link tag
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        if ($name !== 'css') {
            return parent::buildParameterizedReplacement($name, $expression);
        }

        return $this->buildCssLinkTag($expression);
    }

    /**
     * Builds an HTML link tag for CSS inclusion.
     *
     * @param string $expression The CSS directive expression
     * @return string The compiled HTML link tag
     */
    private function buildCssLinkTag(string $expression): string
    {
        $params = $this->parseParameters($expression);
        $fileName = $params[0] ?? '';
        $media = $params[1] ?? 'all';

        if (empty($fileName)) {
            return '<-- CSS directive requires a fileName parameter. -->' . PHP_EOL;
        }

        // Remove quotes from fileName
        $fileName = trim($fileName, '"\'');

        try {
            $absolutePath = $this->pathResolvingService->resolveCss($fileName);
            $resolvedPath = $this->pathResolvingService->resolveAsset($absolutePath);
        } catch (\RuntimeException $e) {
            if ($this->debug) {
                throw new \RuntimeException("Failed to resolve CSS file '{$fileName}': " . $e->getMessage());
            }
            return '<!-- Failed to resolve CSS file: ' . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . ' -->' . PHP_EOL;
        }

        $media = trim($media, '"\'');
        
        return sprintf(
            static::OUTPUT_TEMPLATE,
            htmlspecialchars($resolvedPath, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($media, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Parses parameters from the directive expression.
     *
     * @param string $expression The parameter expression
     * @return array<string> Array of parsed parameters
     */
    private function parseParameters(string $expression): array
    {
        // Simple parameter parsing - splits by comma and trims whitespace
        $params = array_map('trim', explode(',', $expression));
        return array_filter($params, fn($param) => !empty($param));
    }
}
