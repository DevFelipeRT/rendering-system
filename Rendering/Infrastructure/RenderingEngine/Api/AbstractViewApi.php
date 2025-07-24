<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

use Rendering\Domain\Contract\Service\RenderingEngine\Api\ViewApiInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialProviderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ApiContextInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;

/**
 * Provides a base implementation for ViewApi classes.
 *
 * This abstract class encapsulates shared dependencies and logic, while defining
 * abstract methods for stage-specific behaviors. Concrete child classes are
 * required to implement these abstract methods, providing the logic for either
 * the POPULATE or PRESENT rendering stage.
 */
abstract class AbstractViewApi implements ViewApiInterface
{
    /**
     * @param RenderingServiceInterface $renderingService The main rendering service.
     * @param RenderStateInterface $renderState The state object for the current render job.
     * @param ApiContextInterface|null $apiContext The context for the ViewApi.
     */
    public function __construct(
        protected readonly RenderingServiceInterface $renderingService,
        protected readonly RenderStateInterface $renderState,
        protected readonly ?ApiContextInterface $apiContext
    ) {}

    /**
     * {@inheritdoc}
     */
    abstract public function extend(string $layoutName): void;

    /**
     * {@inheritdoc}
     */
    abstract public function startSection(string $sectionName): void;

    /**
     * {@inheritdoc}
     */
    abstract public function stopSection(): void;

    /**
     * {@inheritdoc}
     */
    abstract public function yieldSection(string $sectionName): string;

    /**
     * {@inheritdoc}
     */
    abstract public function startPush(string $stackName): void;

    /**
     * {@inheritdoc}
     */
    abstract public function stopPush(): void;

    /**
     * {@inheritdoc}
     */
    abstract public function renderStack(string $stackName): string;

    /**
     * {@inheritdoc}
     */
    abstract public function shouldRenderOnce(string $id): bool;

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
            return '';
        }
        
        return $this->renderingService->render($partial);
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
        if ($this->apiContext === null) {
            return null;
        }
        
        $contextObjects = $this->apiContext->getContextObjects();
        
        if ($contextObjects === null) {
            return null;
        }

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