<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;

/**
 * Immutable value object representing a header partial view component.
 *
 * This class extends the PartialView to provide a reusable header component
 * that encapsulates its template file reference, rendering data, and nested partial components.
 * It follows the Value Object pattern, ensuring immutability and data integrity through
 * validation at instantiation time.
 *
 * The header component can contain nested partial views, enabling compositional rendering
 * architectures where headers may include sub-components like navigation menus, breadcrumbs,
 * or metadata sections.
 */
final class Header extends PartialView implements HeaderInterface
{
    public readonly ?NavigationInterface $navigation;

    /**
     * Constructor for the Header component.
     *
     * @param string $templateFile The template file path for this header.
     * @param RenderableDataInterface|null $data Optional data for the header template.
     * @param PartialsCollectionInterface|null $partials Optional nested partials collection.
     * @param NavigationInterface|null $navigation The navigation component for the header.
     */
    public function __construct(
        string $templateFile,
        ?RenderableDataInterface $data = null,
        ?PartialsCollectionInterface $partials = null,
        ?NavigationInterface $navigation = null
    ) {
        parent::__construct($templateFile, $data, $partials);
        
        $this->navigation = $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function navigation(): ?NavigationInterface
    {
        return $this->navigation;
    }
}
