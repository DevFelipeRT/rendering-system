<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Page;

use Rendering\Domain\Contract\ValueObject\Renderable\Page\AssetsInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Trait\ValueObject\Renderable\PartialProviderTrait;
use Rendering\Domain\ValueObject\Renderable\Renderable;

/**
 * A composite Value Object that represents a complete, renderable page.
 *
 * It encapsulates all the constituent parts of a page (header, view, footer, etc.)
 * into a single, cohesive, and immutable unit.
 */
final class Page extends Renderable implements PageInterface
{
    use PartialProviderTrait;

    private readonly string $title;
    private readonly string $description;
    private readonly ViewInterface $view;
    private readonly ?AssetsInterface $assets;
    private readonly ?HeaderInterface $header;
    private readonly ?FooterInterface $footer;

    /**
     * Constructs a new Page instance.
     *
     * @param string $layout The layout template for the page.
     * @param ViewInterface $view The main content view object.
     * @param RenderableDataInterface|null $data Optional data for rendering.
     * @param AssetsInterface|null $assets Optional assets (CSS/JS) for the page.
     * @param HeaderInterface|null $header Optional header component.
     * @param FooterInterface|null $footer Optional footer component.
     * @param PartialsCollectionInterface|null $partials Optional collection of partials.
     */
    public function __construct(
        string $layout,
        ViewInterface $view,
        ?RenderableDataInterface $data = null,
        ?AssetsInterface $assets = null,
        ?HeaderInterface $header = null,
        ?FooterInterface $footer = null,
        ?PartialsCollectionInterface $partials = null
    ) {
        $this->title = $view->title();
        $this->description = $view->description();
        $this->view = $view;
        $this->assets = $assets;
        $this->header = $header;
        $this->footer = $footer;
        $this->initializePartials($partials);
        parent::__construct(
            $layout, 
            $data,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function view(): ViewInterface
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function header(): ?HeaderInterface
    {
        return $this->header;
    }

    /**
     * {@inheritdoc}
     */
    public function footer(): ?FooterInterface
    {
        return $this->footer;
    }

    /**
     * {@inheritdoc}
     */
    public function assets(): ?AssetsInterface
    {
        return $this->assets;
    }
}