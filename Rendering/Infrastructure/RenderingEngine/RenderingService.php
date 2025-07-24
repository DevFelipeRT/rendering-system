<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\RenderingEngine;

use LogicException;
use Rendering\Domain\Contract\Service\RenderingEngine\RendererInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Api\ViewApiProvidingServiceInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\Context\ContextBuildingServiceInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateFactoryInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;
use Rendering\Infrastructure\RenderingEngine\Renderer\PageRenderer;
use Rendering\Infrastructure\RenderingEngine\Renderer\Renderer;

/**
 * Main rendering service implementation.
 *
 * This service orchestrates the entire rendering process. It manages the
 * state for each render job using a two-stage (POPULATE and PRESENT) approach
 * to correctly handle complex template hierarchies and stateful directives.
 */
final class RenderingService implements RenderingServiceInterface
{
    private const STAGE_POPULATE = 'POPULATE';
    private const STAGE_PRESENT = 'PRESENT';
    private const STAGE_IDLE = 'IDLE';

    /** @var RenderStateInterface|null The state for the currently active render job. */
    private ?RenderStateInterface $activeState = null;

    /** @var string The current rendering stage. */
    private string $currentStage = self::STAGE_IDLE;

    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly Renderer $defaultRenderer,
        private readonly ViewApiProvidingServiceInterface $viewApiProvider,
        private readonly ContextBuildingServiceInterface $contextBuildingService,
        private readonly RenderStateFactoryInterface $renderStateFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render(RenderableInterface $renderable): string
    {
        $isTopLevelCall = $this->activeState === null;
        if ($isTopLevelCall) {
            $this->activeState = $this->renderStateFactory->create();
        }

        try {
            if ($renderable instanceof PageInterface) {
                // STAGE 1: POPULATE
                $this->currentStage = self::STAGE_POPULATE;
                $this->executeRenderable($renderable);

                // STAGE 2: PRESENT
                $this->currentStage = self::STAGE_PRESENT;
                $layoutName = $this->activeState->getParent() ?? $renderable->fileName();
                
                $data = $this->buildDataFor($renderable);
                $renderer = $this->getRenderer($renderable);
                
                return $renderer->renderTemplate($layoutName, $data);
            }
            
            if ($isTopLevelCall) {
                $this->currentStage = self::STAGE_PRESENT;
            }
            return $this->executeRenderable($renderable);

        } finally {
            if ($isTopLevelCall) {
                $this->activeState = null;
                $this->currentStage = self::STAGE_IDLE;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(string $templateFile, array $templateData = []): string
    {
        $isTopLevelCall = $this->activeState === null;
        if ($isTopLevelCall) {
            $this->activeState = $this->renderStateFactory->create();
            $this->currentStage = self::STAGE_PRESENT;
        }
        
        try {
            // Create a contextless ViewApi for basic functionalities, respecting the current stage.
            $viewApi = $this->viewApiProvider->provideEmpty($this);

            $data = $templateData;
            $data['viewApi'] = $viewApi;
            
            // The renderer's renderTemplate is a simple, low-level method.
            return $this->defaultRenderer->renderTemplate($templateFile, $data);

        } finally {
            if ($isTopLevelCall) {
                $this->activeState = null;
                $this->currentStage = self::STAGE_IDLE;
            }
        }
    }

    /**
     * The core execution unit for a full Renderable object.
     */
    private function executeRenderable(RenderableInterface $renderable): string
    {
        $data = $this->buildDataFor($renderable);
        $renderer = $this->getRenderer($renderable);
        
        if ($this->currentStage === self::STAGE_POPULATE) {
            $renderer->render($renderable, $data);
            return '';
        }
        
        return $renderer->render($renderable, $data);
    }

    private function buildDataFor(RenderableInterface $renderable): array
    {
        $viewApi = $this->viewApiProvider->provideFor($renderable, $this);
        $renderContext = $this->contextBuildingService->buildRenderContext($renderable);

        $data = $renderContext->getData();
        $data['viewApi'] = $viewApi;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function startPush(string $stackName): void
    {
        $this->getActiveStateOrFail()->startPush($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPush(): void
    {
        $this->getActiveStateOrFail()->stopPush();
    }

    /**
     * {@inheritdoc}
     */
    public function renderStack(string $stackName): string
    {
        return $this->getActiveStateOrFail()->renderStack($stackName);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderOnce(string $id): bool
    {
        return $this->getActiveStateOrFail()->shouldRenderOnce($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStage(): string
    {
        if ($this->activeState === null) {
            throw new LogicException('Cannot get stage outside of a rendering operation.');
        }
        return $this->currentStage;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveState(): RenderStateInterface
    {
        return $this->getActiveStateOrFail();
    }

    private function getActiveStateOrFail(): RenderStateInterface
    {
        if ($this->activeState === null) {
            throw new LogicException('Rendering state methods can only be used inside a render() or renderTemplate() call.');
        }
        return $this->activeState;
    }

    private function getRenderer(RenderableInterface $renderable): RendererInterface
    {
        return match (true) {
            $renderable instanceof PageInterface => $this->pageRenderer,
            default => $this->defaultRenderer,
        };
    }
}