<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial\Navigation;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\PartialView;

/**
 * An immutable Value Object representing a navigation menu component.
 *
 * It is composed of a list of NavigationLink objects and can contain its
 * own nested partials, providing a structured and type-safe way to render
 * a navigation bar.
 */
final class Navigation extends PartialView implements NavigationInterface
{
    private readonly NavigationLinkCollectionInterface $links;

    public function __construct(
        string $templateFile, 
        NavigationLinkCollectionInterface $links,
        ?RenderableDataInterface $dataProvider,
        ?PartialsCollectionInterface $partials,
    ) {
        $this->links = $links;
        parent::__construct(
            $templateFile,
            $dataProvider,
            $partials
        );
    }

    /**
     * Returns the collection of navigation links.
     *
     * @return NavigationLink[]
     */
    public function links(): NavigationLinkCollectionInterface
    {
        return $this->links;
    }
}