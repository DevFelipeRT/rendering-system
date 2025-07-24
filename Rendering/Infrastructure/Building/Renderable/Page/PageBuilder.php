<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Page;

use Rendering\Domain\Contract\Service\Building\Page\PageBuilderInterface;
use Rendering\Infrastructure\Building\Renderable\AbstractRenderableBuilder;
use Rendering\Infrastructure\Building\Renderable\Trait\BuilderPartialHandlingTrait;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\AssetsInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Infrastructure\Building\Renderable\Page\AssetsFactory;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Domain\ValueObject\Renderable\Page\Page;

/**
 * Implements the Builder pattern to assemble a complete Page object.
 *
 * This builder provides a fluent API to construct a complex Page object
 * step-by-step. It simplifies the page creation process for the client by
 * encapsulating the assembly of all required components.
 */
final class PageBuilder extends AbstractRenderableBuilder implements PageBuilderInterface
{
    use BuilderPartialHandlingTrait;

    private const DEFAULT_LAYOUT = 'layout/main-layout.phtml';
    private AssetsFactory $assetsFactory;

    /**
     * The main view component for the page.
     * Must be set before building the page.
     * @var ViewInterface|null
     */
    private ?ViewInterface $view = null;

    /**
     * The assets (CSS/JS) associated with the page.
     * @var AssetsInterface|null
     */
    private ?AssetsInterface $assets = null;

    /**
     * The header component for the page.
     * @var HeaderInterface|null
     */
    private ?HeaderInterface $header = null;

    /**
     * The footer component for the page.
     * @var FooterInterface|null
     */
    private ?FooterInterface $footer = null;

    /**
     * Constructor to initialize the PageBuilder with necessary factories.
     * Sets the default layout template file.
     *
     * @param PartialFactory $partialFactory Factory to create partial views.
     * @param RenderableDataFactoryInterface $dataFactory Factory to create renderable data.
     * @param AssetsFactory $assetsFactory Factory to create assets.
     */
    public function __construct(
        PartialFactory $partialFactory,
        RenderableDataFactoryInterface $dataFactory,
        AssetsFactory $assetsFactory
    ) {
        parent::__construct($dataFactory);
        $this->partialFactory = $partialFactory;
        $this->assetsFactory = $assetsFactory;
        $this->initializeTemplateFile(self::DEFAULT_LAYOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLayout(string $layout): self
    {
        $this->setTemplateFile($layout);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setView(ViewInterface $view): self
    {
        $this->view = $view;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssets(array $assets): self
    {
        $this->assets = $this->assetsFactory->createFromArray($assets);
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader(HeaderInterface $header): self
    {
        $this->header = $header;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFooter(FooterInterface $footer): self
    {
        $this->footer = $footer;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): PageInterface
    {
        $this->checkReadyState();
        $this->updatePartials();
        return new Page(
            layout:   $this->templateFile,
            view:     $this->view,
            data:     $this->buildDataFromArray($this->data),
            assets:   $this->assets,
            header:   $this->header,
            footer:   $this->footer,
            partials: $this->buildPartialsCollection($this->partials)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isReady(): bool
    {
        return $this->isConfigured && $this->view !== null;
    }

    private function updatePartials(): void
    {
        $this->header ? $this->addPartial('header', $this->header) : null;
        $this->footer ? $this->addPartial('footer', $this->footer) : null;
    }

}
