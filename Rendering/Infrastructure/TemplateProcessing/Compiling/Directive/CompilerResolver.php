<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\TemplateProcessing\Compiling\Directive;

use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilerFactoryInterface;
use Rendering\Infrastructure\Contract\TemplateProcessing\Compiling\CompilerInterface;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;
use LogicException;

/**
 * Resolves compiler instances by managing dependencies, caching, and creation.
 *
 * This class acts as the central authority for obtaining compiler instances. It
 * uses a declarative map to understand compiler requirements, manages a cache for
 * reusable instances, resolves all necessary dependencies, and delegates the
 * final instantiation to a dedicated factory.
 */
final class CompilerResolver
{
    /**
     * A declarative map defining the configuration for each compiler.
     * @var array<class-string, array{is_cacheable: bool, dependencies: list<string>}>
     */
    private const COMPILERS_MAP = [
        Compiler\Syntax\CommentCompiler::class => ['is_cacheable' => true, 'dependencies' => []],
        Compiler\Syntax\EchoCompiler::class => ['is_cacheable' => true, 'dependencies' => []],
        Compiler\Syntax\PhpCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],

        Compiler\Control\ConditionalCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Control\LoopCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        
        Compiler\Structural\LayoutCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Structural\SectionCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Structural\PartialCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Structural\IncludeCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Structural\YieldCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        
        Compiler\Stateful\StackCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],
        Compiler\Stateful\OnceCompiler::class => ['is_cacheable' => true, 'dependencies' => ['debug']],

        Compiler\Asset\CssCompiler::class => ['is_cacheable' => true, 'dependencies' => ['pathResolvingService', 'debug']],
        Compiler\Asset\JavascriptCompiler::class => ['is_cacheable' => true, 'dependencies' => ['pathResolvingService', 'debug']],
    ];

    /**
     * A cache for storing singleton instances of cacheable compilers.
     * @var array<class-string<CompilerInterface>, CompilerInterface>
     */
    private array $instanceCache = [];

    /**
     * A collection of globally available dependencies for all compilers.
     * @var array<string, mixed>
     */
    private readonly array $globalDependencies;

    public function __construct(
        private readonly CompilerFactoryInterface $compilerFactory,
        PathResolvingServiceInterface $pathResolvingService,
        bool $debug = false
    ) {
        $this->globalDependencies = [
            'pathResolvingService' => $pathResolvingService,
            'debug' => $debug,
        ];
    }

    /**
     * Resolves a compiler instance by its fully-qualified class name.
     *
     * @param class-string<CompilerInterface> $compilerClass The FQCN of the compiler to resolve.
     * @return CompilerInterface The resolved compiler instance.
     */
    public function resolve(string $compilerClass): CompilerInterface
    {
        $definition = $this->getCompilerDefinition($compilerClass);
        $isCacheable = $definition['is_cacheable'];

        if ($isCacheable && isset($this->instanceCache[$compilerClass])) {
            return $this->instanceCache[$compilerClass];
        }

        $args = $this->prepareArguments($definition);
        $compiler = $this->compilerFactory->create($compilerClass, $args);

        if ($isCacheable) {
            $this->instanceCache[$compilerClass] = $compiler;
        }

        return $compiler;
    }

    /**
     * Retrieves and validates the definition for a given compiler from the map.
     */
    private function getCompilerDefinition(string $compilerClass): array
    {
        if (!isset(self::COMPILERS_MAP[$compilerClass])) {
            throw new LogicException("Compiler definition not found for '{$compilerClass}'.");
        }
        return self::COMPILERS_MAP[$compilerClass];
    }

    /**
     * Prepares the ordered list of constructor arguments for a compiler.
     */
    private function prepareArguments(array $definition): array
    {
        $args = [];
        foreach ($definition['dependencies'] as $depName) {
            if (!array_key_exists($depName, $this->globalDependencies)) {
                throw new LogicException("Unresolved dependency: '{$depName}' is not available for the compiler.");
            }
            $args[] = $this->globalDependencies[$depName];
        }

        return $args;
    }
}