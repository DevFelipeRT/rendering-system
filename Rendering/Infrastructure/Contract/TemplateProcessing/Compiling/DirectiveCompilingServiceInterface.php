<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\TemplateProcessing\Compiling;

/**
 * Service responsible for compiling template directives in the correct order.
 */
interface DirectiveCompilingServiceInterface
{
    /**
     * Applies all directive compilers to the content in the correct order.
     *
     * @param string $content Content to compile
     * 
     * @return string Compiled content
     */
    public function compileDirectives(string $content): string;
}