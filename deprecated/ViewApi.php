<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

use Rendering\Domain\Contract\Service\RenderingEngine\Api\ViewApiInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialProviderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\RenderContextInterface;

/**
 * Provides a public API for templates to interact with the rendering system.
 *
 * This class is injected into every template's scope and acts as a safe
 * bridge to the underlying rendering engine via the RenderingServiceInterface.
 */
final class ViewApi implements ViewApiInterface
{
    /**
     * @param RenderingServiceInterface $renderingService
     * @param RenderContextInterface|null $renderContext
     */
    public function __construct(
        private readonly RenderingServiceInterface $renderingService,
        private readonly ?RenderContextInterface $renderContext
    ) {}

    /**
     * {@inheritdoc}
     */
    public function include(string $templateFile, array $data = []): string
    {
        return $this->renderingService->renderTemplate($templateFile, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderPartial(string $identifier, array $data = []): string
    {
        $partial = $this->findPartialByIdentifier($identifier);

        if ($partial === null) {
            return "";
        }

        return $this->renderingService->render($partial);
    }

    /**
     * {@inheritdoc}
     */
    public function startPush(string $stackName): void
    {
        $this->renderingService->startPush($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPush(): void
    {
        $this->renderingService->stopPush();
    }

    /**
     * {@inheritdoc}
     */
    public function renderStack(string $stackName): string
    {
        return $this->renderingService->renderStack($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderOnce(string $id): bool
    {
        return $this->renderingService->shouldRenderOnce($id);
    }

    /**
     * Finds a partial by its identifier within the current rendering context.
     *
     * @param string $identifier The unique identifier for the partial.
     * @return RenderableInterface|null The found partial or null if not found.
     */
    private function findPartialByIdentifier(string $identifier): ?RenderableInterface
    {
        $partialsCollections = $this->retrieveContextPartials();

        if ($partialsCollections === null) {
            return null;
        }

        foreach ($partialsCollections as $partialsCollection) {
            if ($partialsCollection->has($identifier)) {
                return $partialsCollection->get($identifier);
            }
        }
    
        return null;
    }

    /**
     * Retrieves all partial collections from the current context.
     *
     * @return array<PartialsCollectionInterface>|null An array of collections or null if none are available.
     */
    private function retrieveContextPartials(): ?array
    {
        if ($this->renderContext === null) {
            return null;
        }
        
        $contextObjects = $this->renderContext->getApiContext();
        
        $partialsCollections = [];
        foreach ($contextObjects as $context) {
            if ($context instanceof PartialProviderInterface) {
                $collection = $context->partials();
                if ($collection !== null) {
                    $partialsCollections[] = $collection;
                }
            }
        }

        if (empty($partialsCollections)) {
            return null;
        }

        return $partialsCollections;
    }
}