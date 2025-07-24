<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine\Api;

use InvalidArgumentException;
use Rendering\Domain\Contract\Service\RenderingEngine\Api\ViewApiInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Api\ViewApiProvidingServiceInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuildingServiceInterface;

/**
 * Provides configured ViewApi instances based on the rendering stage.
 *
 * This service acts as a factory for creating stage-specific ViewApi objects,
 * ensuring that the correct API (Populating or Presenting) is used during
 * the appropriate phase of the rendering process.
 */
final class ViewApiProvidingService implements ViewApiProvidingServiceInterface
{
    /**
     * A map dispatching a stage name to a concrete ViewApi class.
     * @var array<string, class-string<ViewApiInterface>>
     */
    private readonly array $apiMap;

    /**
     * @param ContextBuildingServiceInterface $contextBuildingService The service that builds the contexts.
     * @param array<string, class-string<ViewApiInterface>> $apiMap A map of stage names to ViewApi class names.
     */
    public function __construct(
        private readonly ContextBuildingServiceInterface $contextBuildingService,
        array $apiMap
    ) {
        $this->apiMap = $apiMap;
    }

    /**
     * {@inheritdoc}
     */
    public function provideFor(
        RenderableInterface $renderable,
        RenderingServiceInterface $renderingService
    ): ViewApiInterface {
        $stage = $renderingService->getCurrentStage();
        $renderState = $renderingService->getActiveState();

        $apiClass = $this->findApiClassForStage($stage);

        // Uses the ContextBuildingService to build only the required ApiContext.
        $apiContext = $this->contextBuildingService->buildApiContext($renderable);

        return new $apiClass($renderingService, $renderState, $apiContext);
    }

    /**
     * {@inheritdoc}
     */
    public function provideEmpty(
        RenderingServiceInterface $renderingService
    ): ViewApiInterface {
        $stage = $renderingService->getCurrentStage();
        $renderState = $renderingService->getActiveState();

        $apiClass = $this->findApiClassForStage($stage);

        return new $apiClass($renderingService, $renderState, null);
    }

    /**
     * Finds the appropriate ViewApi class for the given rendering stage.
     *
     * @param string $stage The rendering stage (e.g., 'POPULATE' or 'PRESENT').
     * @return class-string<ViewApiInterface> The fully qualified class name of the ViewApi.
     * @throws InvalidArgumentException if no ViewApi is mapped to the given stage.
     */
    private function findApiClassForStage(string $stage): string
    {
        if (!isset($this->apiMap[$stage])) {
            throw new InvalidArgumentException("No ViewApi implementation found for stage: {$stage}");
        }

        return $this->apiMap[$stage];
    }
}