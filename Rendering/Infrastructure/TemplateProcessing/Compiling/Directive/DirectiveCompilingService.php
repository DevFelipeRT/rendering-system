<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive;

use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\DirectiveCompilingServiceInterface;
use Rendering\Infrastructure\TemplateProcessing\Compiling\Directive\Compiler;

/**
 * Service responsible for applying directive compilers in the correct order.
 */
final class DirectiveCompilingService implements DirectiveCompilingServiceInterface
{
    /**
     * Defines the fixed, explicit order in which compiler passes must be executed.
     */
    private const COMPILATION_ORDER = [
        Compiler\Syntax\CommentCompiler::class,
        Compiler\Structural\LayoutCompiler::class,
        Compiler\Structural\SectionCompiler::class,
        Compiler\Syntax\PhpCompiler::class,
        Compiler\Syntax\EchoCompiler::class,
        Compiler\Control\ConditionalCompiler::class,
        Compiler\Control\LoopCompiler::class,
        Compiler\Structural\PartialCompiler::class,
        Compiler\Structural\IncludeCompiler::class,
        Compiler\Structural\YieldCompiler::class,
        Compiler\Asset\CssCompiler::class,
        Compiler\Asset\JavascriptCompiler::class,
        Compiler\Stateful\StackCompiler::class,
        Compiler\Stateful\OnceCompiler::class
    ];

    private readonly CompilerResolver $compilerResolver;

    public function __construct(
        CompilerResolver $compilerResolver
    ) {
        $this->compilerResolver = $compilerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function compileDirectives(string $content): string
    {
        foreach (self::COMPILATION_ORDER as $compilerClass) {
            $compiler = $this->compilerResolver->resolve($compilerClass);
            $content = $compiler->compile($content);
        }
        return $content;
    }
}