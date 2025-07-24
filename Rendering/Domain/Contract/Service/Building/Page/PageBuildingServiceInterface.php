<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Page;

use Rendering\Domain\Contract\ValueObject\Renderable\Page\PageInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;

interface PageBuildingServiceInterface
{
    /**
     * Creates a Page object with the specified parameters.
     *
     * @param string $layout The layout template file for the page.
     * @param array $data Data to be passed to the view.
     * @param array $partials Partials to be included in the page.
     * @param ViewInterface $view The view object.
     * @param HeaderInterface $header The header object.
     * @param FooterInterface $footer The footer object.
     * @param array $assets Assets to be included in the page.
     *
     * @return PageInterface The constructed Page object.
     */
    public function createPage(
        string $layout,
        ViewInterface $view,
        array $data = [],
        array $partials = [],
        array $assets = [],
        ?HeaderInterface $header = null,
        ?FooterInterface $footer = null,
    ): PageInterface;

    /**
     * Retrieves the current building Page object.
     * 
     * @return PageBuilderInterface The current PageBuilderInterface instance.
     */
    public function pageBuilder(): PageBuilderInterface;
}
