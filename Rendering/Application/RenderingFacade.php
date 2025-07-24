<?php

declare(strict_types=1);

namespace Rendering\Application;

use Rendering\Domain\Contract\Service\Building\BuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Page\PageBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\FooterBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\HeaderBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\NavigationBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuilderInterface;
use Rendering\Domain\Contract\Service\Building\View\ViewBuilderInterface;
use Rendering\Domain\Contract\Service\RenderingEngine\RenderingServiceInterface;
use Rendering\Domain\Contract\Service\RenderingFacadeInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Infrastructure\Contract\RenderingEngine\State\RenderStateInterface;

/**
 * The concrete implementation of the Rendering Facade.
 *
 * It orchestrates the page building and rendering services to provide a simple,
 * unified API for the application. This is the primary entry point for all
 * rendering tasks.
 */
final class RenderingFacade implements RenderingFacadeInterface
{
    /**
     * @param BuildingServiceInterface $buildingService The service responsible for assembling Page objects.
     * @param RenderingServiceInterface $renderingService The service responsible for rendering objects into HTML.
     */
    public function __construct(
        private readonly BuildingServiceInterface $buildingService,
        private readonly RenderingServiceInterface $renderingService
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(string $layout, array $data = [], array $partials = []): self
    {
        $this->buildingService->setPage($layout, $data, $partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssets(array $assets): self
    {
        $this->buildingService->setAssets($assets);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setView(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->buildingService->setView($templateFile, $data, $partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title): self
    {
        $this->buildingService->setTitle($title);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): self
    {
        $this->buildingService->setDescription($description);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setHeader(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->buildingService->setHeader($templateFile, $data, $partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigation(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->buildingService->setNavigation($templateFile, $data, $partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addNavigationLink(
        string $label,
        string $url,
        bool $visible = true,
        bool $active = false,
        string $iconClass = '',
    ): self {
        $this->buildingService->addNavigationLink($label, $url, $visible, $active, $iconClass);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigationLinks(array $links): self
    {
        $this->buildingService->setNavigationLinks($links);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFooter(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->buildingService->setFooter($templateFile, $data, $partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright(string $owner, string $message = 'All rights reserved.'): self
    {
        $this->buildingService->setCopyright($owner, $message);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): PageInterface
    {
        return $this->buildingService->build();
    }

    /**
     * {@inheritdoc}
     */
    public function render(?RenderableInterface $page = null): string
    {
        $pageToRender = $page ?? $this->buildingService->build();
        return $this->renderingService->render($pageToRender);
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(string $templateFile, array $templateData = []): string
    {
        return $this->renderingService->renderTemplate($templateFile, $templateData);
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
     * {@inheritdoc}
     */
    public function getCurrentStage(): string
    {
        return $this->renderingService->getCurrentStage();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveState(): RenderStateInterface
    {
        return $this->renderingService->getActiveState();
    }

    /**
     * {@inheritdoc}
     */
    public function createHeader(
        string $templateFile,
        array $data = [],
        array $partials = [],
        ?NavigationInterface $navigation = null
    ): HeaderInterface {
        return $this->buildingService->createHeader($templateFile, $data, $partials, $navigation);
    }

    /**
     * {@inheritdoc}
     */
    public function createFooter(
        string $templateFile,
        array $data = [],
        array $partials = [],
        ?string $copyrightOwner = null,
        ?string $copyrightMessage = 'All rights reserved.'
    ): FooterInterface {
        return $this->buildingService->createFooter($templateFile, $data, $partials, $copyrightOwner, $copyrightMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function createNavigation(
        string $templateFile,
        array $data = [],
        array $partials = [],
        array $links = []
    ): NavigationInterface {
        return $this->buildingService->createNavigation($templateFile, $data, $partials, $links);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(
        string $templateFile,
        array $data = [],
        array $partials = [],
        ?string $title = null,
        ?string $description = null
    ): ViewInterface {
        return $this->buildingService->createView($templateFile, $data, $partials, $title, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function createPartialView(
        string $templateFile,
        array $data = [],
        array $partials = []
    ): PartialViewInterface {
        return $this->buildingService->createPartialView($templateFile, $data, $partials);
    }

    /**
     * {@inheritdoc}
     */
    public function createPage(
        string $layout,
        ViewInterface $view,
        array $data = [],
        array $partials = [],
        array $assets = [],
        ?HeaderInterface $header = null,
        ?FooterInterface $footer = null
    ): PageInterface {
        return $this->buildingService->createPage(
            $layout,
            $view,
            $data,
            $partials,
            $assets,
            $header,
            $footer
        );
    }

    /**
     * {@inheritdoc}
     */
    public function headerBuilder(): HeaderBuilderInterface
    {
        return $this->buildingService->headerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function footerBuilder(): FooterBuilderInterface
    {
        return $this->buildingService->footerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function navigationBuilder(): NavigationBuilderInterface
    {
        return $this->buildingService->navigationBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function partialBuilder(): PartialBuilderInterface
    {
        return $this->buildingService->partialBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function viewBuilder(): ViewBuilderInterface
    {
        return $this->buildingService->viewBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function pageBuilder(): PageBuilderInterface
    {
        return $this->buildingService->pageBuilder();
    }
}