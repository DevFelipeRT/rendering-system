<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\Asset;

use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler\AbstractDirectiveCompiler;

/**
 * Compiler for JavaScript-related directives in templates.
 * 
 * Handles directives like:
 * - @js('fileName') - includes a JavaScript file
 * - @js('fileName', 'defer') - includes a JavaScript file with defer attribute
 * - @js('fileName', 'async') - includes a JavaScript file with async attribute
 */
final class JavascriptCompiler extends AbstractDirectiveCompiler
{
    /**
     * Directives that require parameters in parentheses.
     */
    protected const PARAMETERIZED_DIRECTIVES = [
        'js' => '/\@js\s*\(/',
    ];

    /**
     * A template for the output of JavaScript directives.
     */
    protected const OUTPUT_TEMPLATE = '<script type="text/javascript" src="%s"%s></script>';

    /**
     * @param PathResolvingServiceInterface $pathResolvingService Service to resolve JavaScript file paths
     */
    public function __construct(
        private readonly PathResolvingServiceInterface $pathResolvingService,
        bool $debug
    ) {
        parent::__construct($debug);
    }

    /**
     * Builds the replacement for JavaScript directives.
     *
     * @param string $name The directive name ('js')
     * @param string $expression The expression within parentheses
     * @return string The compiled HTML script tag
     */
    protected function buildParameterizedReplacement(string $name, string $expression): string
    {
        if ($name !== 'js') {
            return parent::buildParameterizedReplacement($name, $expression);
        }

        return $this->buildJsScriptTag($expression);
    }

    /**
     * Builds an HTML script tag for JavaScript inclusion.
     *
     * @param string $expression The JavaScript directive expression
     * @return string The compiled HTML script tag
     */
    private function buildJsScriptTag(string $expression): string
    {
        $params = $this->parseParameters($expression);
        $fileName = $params[0] ?? '';
        $loadType = $params[1] ?? '';

        if (empty($fileName)) {
            return '<-- JavaScript directive requires a fileName parameter. -->' . PHP_EOL;
        }

        // Remove quotes from fileName
        $fileName = trim($fileName, '"\'');

        try {
            $absolutePath = $this->pathResolvingService->resolveJavascript($fileName);
            $resolvedPath = $this->pathResolvingService->resolveAsset($absolutePath);
        } catch (\RuntimeException $e) {
            if ($this->debug) {
                throw new \RuntimeException("Failed to resolve JavaScript file '{$fileName}': " . $e->getMessage());
            }
            return '<!-- Failed to resolve JavaScript file: ' . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . ' -->' . PHP_EOL;
        }

        $loadType = trim($loadType, '"\'');
        $attributes = $this->buildLoadAttributes($loadType);
        
        return sprintf(
            static::OUTPUT_TEMPLATE,
            htmlspecialchars($resolvedPath, ENT_QUOTES, 'UTF-8'),
            $attributes
        );
    }

    /**
     * Builds loading attributes for script tag.
     *
     * @param string $loadType The loading type ('defer', 'async', or empty)
     * @return string The formatted attributes string
     */
    private function buildLoadAttributes(string $loadType): string
    {
        return match (strtolower($loadType)) {
            'defer' => ' defer',
            'async' => ' async',
            default => ''
        };
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
