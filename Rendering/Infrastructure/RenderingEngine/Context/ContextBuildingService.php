<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Context;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuilderInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuildingServiceInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ApiContextInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\RenderContextInterface;
use Rendering\Infrastructure\RenderingEngine\Exception\ContextBuilderException;

/**
 * An orchestrator that builds rendering contexts by dispatching the task
 * to a specialized builder, using a cache for high-performance lookups.
 */
final class ContextBuildingService implements ContextBuildingServiceInterface
{
    /**
     * @var array<class-string, ContextBuilderInterface>
     */
    private array $lookupCache = [];

    /**
     * @var array<string, array{0: RenderContextInterface, 1: ApiContextInterface}>
     */
    private array $resultsCache = [];

    /**
     * @param array<class-string<RenderableInterface>, ContextBuilderInterface> $builders
     */
    public function __construct(private readonly array $builders)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildRenderContext(RenderableInterface $renderable): RenderContextInterface
    {
        return $this->getOrBuildBoth($renderable)[0];
    }

    /**
     * {@inheritdoc}
     */
    public function buildApiContext(RenderableInterface $renderable): ApiContextInterface
    {
        return $this->getOrBuildBoth($renderable)[1];
    }

    /**
     * Builds both contexts if not already cached for the given renderable object.
     *
     * @return array{0: RenderContextInterface, 1: ApiContextInterface}
     */
    private function getOrBuildBoth(RenderableInterface $renderable): array
    {
        $objectHash = spl_object_hash($renderable);

        if (isset($this->resultsCache[$objectHash])) {
            return $this->resultsCache[$objectHash];
        }

        $builder = $this->findBuilderFor($renderable);
        $result = $builder->build($renderable);

        $this->resultsCache[$objectHash] = $result;

        return $result;
    }

    /**
     * Finds the appropriate context builder by first checking the cache.
     */
    private function findBuilderFor(RenderableInterface $renderable): ContextBuilderInterface
    {
        $class = get_class($renderable);

        return $this->lookupCache[$class]
            ?? $this->resolveAndCacheBuilder($renderable, $class);
    }

    /**
     * Resolves the correct builder by iterating through the map and caches the result.
     *
     * @param class-string $class
     */
    private function resolveAndCacheBuilder(RenderableInterface $renderable, string $class): ContextBuilderInterface
    {
        foreach ($this->builders as $type => $builder) {
            if ($renderable instanceof $type) {
                $this->lookupCache[$class] = $builder;
                return $builder;
            }
        }

        throw ContextBuilderException::forNotFound($class);
    }
}