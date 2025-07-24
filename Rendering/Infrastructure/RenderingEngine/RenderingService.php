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
        // A top-level call is the first entry into this method for a given request.
        // Nested calls (like @include) will reuse the existing active state.
        $isTopLevelCall = $this->activeState === null;
        if ($isTopLevelCall) {
            $this->activeState = $this->renderStateFactory->create();
        }

        try {
            // Delegate the rendering logic based on the type of the renderable object.
            if ($renderable instanceof PageInterface) {
                return $this->renderPage($renderable);
            }
            
            // For simple renderables (like partials in an @include),
            // ensure the stage is set to PRESENT if this is a top-level call.
            if ($isTopLevelCall) {
                $this->currentStage = self::STAGE_PRESENT;
            }
            return $this->executeRenderable($renderable);

        } finally {
            // Clean up the state only after the entire top-level operation is complete.
            if ($isTopLevelCall) {
                $this->activeState = null;
                $this->currentStage = self::STAGE_IDLE;
            }
        }
    }

    /**
     * Orchestrates the two-stage rendering process for a Page object.
     */
    private function renderPage(PageInterface $page): string
    {
        // STAGE 1: POPULATE - Traverse the entire inheritance hierarchy to collect state.
        $baseLayoutName = $this->runPopulationStage($page);

        echo '<pre>';
            var_dump($this->activeState);
        echo '</pre>';

        die('--- Fim da Depuração do Estágio POPULATE ---');
        
        // STAGE 2: PRESENT - Render the final base layout using the collected state.
        return $this->runPresentationStage($page, $baseLayoutName);
    }

    /**
     * Executes the POPULATE stage.
     *
     * This method traverses the @extends chain from the page's view upwards,
     * executing each template to populate the RenderState with sections and stacks.
     *
     * @return string The filename of the final, top-level base layout.
     */
    private function runPopulationStage(PageInterface $page): string
    {
        $this->currentStage = self::STAGE_POPULATE;

        // Build the data context for the page ONCE. This context, including the
        // correctly configured PopulatingViewApi, will be passed up the entire
        // inheritance chain.
        $pageData = $this->buildDataFor($page);
        
        // Start the population process by "rendering" the page's own view template.
        // This triggers the first @extends and populates the initial sections/stacks.
        $this->pageRenderer->render($page, $pageData);
        
        $parentName = $this->activeState->getParent();
        $finalLayoutName = $parentName ?? $page->view()->fileName();

        // Now, loop through the parent layouts until we reach the root.
        while ($parentName !== null) {
            $this->activeState->setParent(null); // Reset for the next iteration.
            
            // Execute the parent layout, passing the same full data context.
            $this->renderTemplate($parentName, $pageData);
            
            // Check if this parent extends another layout.
            $parentName = $this->activeState->getParent();
            if ($parentName) {
                $finalLayoutName = $parentName; // Update the final layout name.
            }
        }

        return $finalLayoutName;
    }

    /**
     * Executes the PRESENT stage.
     *
     * This method renders the final base layout. The PresentingViewApi will now
     * read from the populated RenderState to correctly output sections and stacks.
     */
    private function runPresentationStage(PageInterface $page, string $baseLayoutName): string
    {
        $this->currentStage = self::STAGE_PRESENT;

        // Rebuild the data context, this time getting a PresentingViewApi.
        $layoutData = $this->buildDataFor($page);
        
        // Render the final base layout template.
        return $this->defaultRenderer->renderTemplate($baseLayoutName, $layoutData);
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
            $data = $templateData;

            // If a ViewApi isn't already provided (which it will be during the
            // population chain), create a default, empty one.
            if (!isset($data['viewApi'])) {
                $viewApi = $this->viewApiProvider->provideEmpty($this);
                $data['viewApi'] = $viewApi;
            }
            
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
        
        return $renderer->render($renderable, $data);
    }

    /**
     * Prepares the complete data array for a given renderable.
     * This involves creating the appropriate ViewApi (Populating or Presenting)
     * and fetching the template data from the context builder.
     */
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
