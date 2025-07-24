<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\View;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Trait\ValueObject\Renderable\PartialProviderTrait;
use Rendering\Domain\ValueObject\Renderable\Renderable;

/**
 * An immutable Value Object representing the main content of a page.
 *
 * It encapsulates a specific page's template file and its required data.
 */
final class View extends Renderable implements ViewInterface
{
    use PartialProviderTrait;

    /**
     * @var string The title of the view, which can be used for SEO or page headers.
     */
    private readonly string $title;

    /**
     * @var string The description of the view, typically used for SEO purposes.
     */
    private readonly string $description;

    /**
     * Constructs a new View instance.
     *
     * @param string $templateFile The path to the template file for this view.
     * @param RenderableDataInterface|null $dataProvider Optional data provider for dynamic content.
     * @param PartialsCollectionInterface|null $partials Optional collection of partials to include in the view.
     * @param string|null $title Optional title for the view, defaults to an empty string if not provided.
     */
    public function __construct(
        string $templateFile, 
        ?RenderableDataInterface $dataProvider,
        ?PartialsCollectionInterface $partials = null,
        ?string $title = null,
        ?string $description = null
    ) {
        $this->title = $title ?? '';
        $this->description = $description ?? '';
        $this->initializePartials($partials);
        parent::__construct($templateFile, $dataProvider);
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
}