<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\RenderableInterface;

/**
 * Defines the contract for a reusable partial view component.
 *
 * A Partial View is a self-contained, renderable component that encapsulates
 * its own template, data, and can also contain nested partial sub-components.
 * It combines the ability to be rendered with the ability to provide partials.
 */
interface PartialViewInterface extends RenderableInterface, PartialProviderInterface
{
}
