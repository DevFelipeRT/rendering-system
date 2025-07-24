<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\HeaderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationInterface;
use Rendering\Domain\Contract\Service\Building\Exception\BuilderExceptionInterface;

/**
 * Defines the contract for a Header component builder.
 *
 * This interface extends the PartialBuilderInterface to provide specific
 * methods for building a Header object, which is a type of Partial.
 */
interface HeaderBuilderInterface extends PartialBuilderInterface
{
    /**
     * Sets the navigation component for the header.
     *
     * @param NavigationInterface $navigation The navigation component.
     * @return self
     */
    public function setNavigation(NavigationInterface $navigation): self;

    /**
     * Builds the header component.
     *
     * @return HeaderInterface The built header object.
     * @throws BuilderExceptionInterface If the builder is not in a ready state.
     */
    public function build(): HeaderInterface;
}
