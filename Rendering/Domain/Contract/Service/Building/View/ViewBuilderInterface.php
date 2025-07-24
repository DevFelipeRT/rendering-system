<?php 

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\View;

use Rendering\Domain\Contract\Service\Building\RenderableBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\View\ViewInterface;
use Rendering\Domain\Contract\Service\Building\Exception\BuilderExceptionInterface;


/**
 * Interface for building a view object with a specific template, data, and partials.
 *
 * This interface extends the RenderableBuilderInterface to provide additional
 * methods specific to building views.
 */
interface ViewBuilderInterface extends RenderableBuilderInterface
{
    /**
     * Sets the title for the view.
     *
     * @param string $title The title of the view.
     * @return self
     */
    public function setTitle(string $title): self;

    /**
     * Sets the description for the view.
     *
     * @param string $description The description of the view.
     * @return self
     */
    public function setDescription(string $description): self;

    /**
     * Builds and returns the View object.
     *
     * @return ViewInterface The constructed View object.
     * @throws BuilderExceptionInterface If the builder is not in a ready state.
     */
    public function build(): ViewInterface;
}