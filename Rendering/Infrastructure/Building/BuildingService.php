<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building;

use Rendering\Domain\Contract\Service\Building\BuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Page\PageBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Page\PageBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Partial\HeaderBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\FooterBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\NavigationBuilderInterface;
use Rendering\Domain\Contract\Service\Building\Partial\PartialBuilderInterface;
use Rendering\Domain\Contract\Service\Building\View\ViewBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\View\ViewBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialViewInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Infrastructure\Contract\PathResolving\PathResolvingServiceInterface;

/**
 * A high-level service that acts as a Facade for the page building process.
 *
 * This service provides a simple, procedural API for clients (like controllers)
 * to assemble a complete Page object by orchestrating a suite of specialized
 * builders for each part of the page.
 */
final class BuildingService implements BuildingServiceInterface
{
    /** @var PageBuildingServiceInterface The service for page building operations. */
    private PageBuildingServiceInterface $pageBuildingService;

    /** @var ViewBuildingServiceInterface The service for view building operations. */
    private ViewBuildingServiceInterface $viewBuildingService;

    /** @var PartialBuildingServiceInterface A specialized service for managing all partial-related operations. */
    private PartialBuildingServiceInterface $partialBuildingService;

    /** @var PathResolvingServiceInterface Service for resolving paths. */
    private PathResolvingServiceInterface $pathResolvingService;

    /**
     * @param PageBuildingServiceInterface $pageBuildingService The service for page building operations.
     * @param ViewBuildingServiceInterface $viewBuildingService The service for view building operations.
     * @param PartialBuildingServiceInterface $partialBuildingService A specialized service for managing all partial-related operations.
     * @param PathResolvingServiceInterface $pathResolvingService Service for resolving paths.
     */
    public function __construct(
        PageBuildingServiceInterface $pageBuildingService,
        ViewBuildingServiceInterface $viewBuildingService,
        PartialBuildingServiceInterface $partialBuildingService,
        PathResolvingServiceInterface $pathResolvingService
    ) {
        $this->pageBuildingService = $pageBuildingService;
        $this->viewBuildingService = $viewBuildingService;
        $this->partialBuildingService = $partialBuildingService;
        $this->pathResolvingService = $pathResolvingService;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(string $layout, array $data = [], array $partials = []): self
    {
        $this->pageBuilder()
        ->setLayout($layout)
        ->setData($data)
        ->setPartials($partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssets(array $assets): self
    {
        $resolvedAssets = $this->resolveAssets($assets);

        $this->pageBuilder()
        ->setAssets($resolvedAssets);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setView(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->viewBuilder()
        ->setTemplateFile($templateFile)
        ->setData($data)
        ->setPartials($partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title): self
    {
        $this->viewBuilder()->setTitle($title);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): self
    {
        $this->viewBuilder()->setDescription($description);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setHeader(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->headerBuilder()
        ->setTemplateFile($templateFile)
        ->setData($data)
        ->setPartials($partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigation(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->navigationBuilder()
        ->setTemplateFile($templateFile)
        ->setData($data)
        ->setPartials($partials);
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
        $this->navigationBuilder()
        ->addNavigationLink($label, $url, $active);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigationLinks(array $links): self
    {
        $this->navigationBuilder()
        ->setNavigationLinks($links);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFooter(string $templateFile, array $data = [], array $partials = []): self
    {
        $this->footerBuilder()
        ->setTemplateFile($templateFile)
        ->setData($data)
        ->setPartials($partials);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright(string $owner, string $message = 'All rights reserved.'): self
    {
        $this->footerBuilder()
        ->setCopyright($owner, $message);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): PageInterface
    {
        $components = $this->buildComponents();
        $this->setComponents(
            $components['view'],
            $components['header'],
            $components['footer']
        );
        return $this->pageBuilder()->build();
    }

    private function resolveAssets(array $assets): array
    {
        $resolvedAssets = [];
        foreach ($assets as $assetPath) {
            if (!is_string($assetPath)) {
                throw new \InvalidArgumentException('Assets must be a flat array of strings.');
            }
            $resolvedAssets[] = $this->pathResolvingService->resolveAsset($assetPath);
        }
        return $resolvedAssets;
    }

    private function buildComponents(): array
    {
        $view = $this->viewBuilder()->build();
        $navigation = $this->buildNavigation();
        $header = $this->buildHeader($navigation);
        $footer = $this->buildFooter();
        return [
            'view' => $view,
            'header' => $header,
            'footer' => $footer,
        ];
    }

    private function buildNavigation(): ?NavigationInterface
    {
        return $this->navigationBuilder()->isReady()
            ? $this->navigationBuilder()->build()
            : null;
    }

    private function buildHeader(?NavigationInterface $navigation = null): ?HeaderInterface
    {
        $navigation ? $this->headerBuilder()->setNavigation($navigation) : null;
        return $this->headerBuilder()->isReady()
            ? $this->headerBuilder()->build()
            : null;
    }

    private function buildFooter(): ?FooterInterface
    {
        return $this->footerBuilder()->isReady()
            ? $this->footerBuilder()->build()
            : null;
    }
    
    private function setComponents(
        ViewInterface $view,
        ?HeaderInterface $header,
        ?FooterInterface $footer,
    ): void {
        $view ? $this->pageBuilder()->setView($view) : null;
        $header ? $this->pageBuilder()->setHeader($header) : null;
        $footer ? $this->pageBuilder()->setFooter($footer) : null;
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
        return $this->partialBuildingService->createHeader($templateFile, $data, $partials, $navigation);
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
        return $this->partialBuildingService->createFooter($templateFile, $data, $partials, $copyrightOwner, $copyrightMessage);
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
        return $this->partialBuildingService->createNavigation($templateFile, $data, $partials, $links);
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
        return $this->viewBuildingService->createView($templateFile, $data, $partials, $title, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function createPartialView(
        string $templateFile, 
        array $data = [], 
        array $partials = []
    ): PartialViewInterface {
        return $this->partialBuildingService->createPartialView($templateFile, $data, $partials);
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
        ?FooterInterface $footer = null,
    ): PageInterface {
        return $this->pageBuildingService->createPage(
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
        return $this->partialBuildingService->headerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function footerBuilder(): FooterBuilderInterface
    {
        return $this->partialBuildingService->footerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function navigationBuilder(): NavigationBuilderInterface
    {
        return $this->partialBuildingService->navigationBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function partialBuilder(): PartialBuilderInterface
    {
        return $this->partialBuildingService->partialBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function viewBuilder(): ViewBuilderInterface
    {
        return $this->viewBuildingService->viewBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function pageBuilder(): PageBuilderInterface
    {
        return $this->pageBuildingService->pageBuilder();
    }
}
