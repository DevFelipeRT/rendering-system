<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Page;

use Rendering\Domain\Contract\Service\Building\Page\PageBuildingServiceInterface;
use Rendering\Domain\Contract\Service\Building\Page\PageBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;

/**
 * A service that orchestrates the building of complete Page objects.
 *
 * This service provides a high-level API for assembling pages by coordinating
 * the specialized builders for views, partials, and other page components.
 * It simplifies the page construction process while maintaining flexibility
 * through the underlying builder pattern.
 */
final class PageBuildingService implements PageBuildingServiceInterface
{
    /**
     * @param PageBuilderInterface $pageBuilder The primary builder for the Page.
     */
    public function __construct(
        private readonly PageBuilderInterface $pageBuilder,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function pageBuilder(): PageBuilderInterface
    {
        return $this->pageBuilder;
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
        $this->pageBuilder()
            ->setLayout($layout)
            ->setView($view)
            ->setAssets($assets)
            ->setData($data)
            ->setPartials($partials);

        if ($header !== null) {
            $this->pageBuilder()->setHeader($header);
        }

        if ($footer !== null) {
            $this->pageBuilder()->setFooter($footer);
        }

        return $this->pageBuilder()->build();
    }
}
