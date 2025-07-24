<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial;

/**
 * Contract for footer partial view components.
 *
 * This interface extends PartialViewInterface to define the contract
 * for footer components that are typically rendered at the bottom
 * of pages. Footer components often contain copyright information,
 * links, contact details, and other supplementary content.
 *
 * By implementing this interface, footer components ensure they
 * can be properly rendered within the page composition system
 * while maintaining type safety and architectural consistency.
 */
interface FooterInterface extends PartialViewInterface
{

    /**
     * Retrieves the copyright information for the footer.
     *
     * @return array<string, string> An associative array containing:
     * - 'owner': The name of the copyright owner.
     * - 'message': The copyright message (default: 'All rights reserved.').
     * - 'year': The copyright year (default: current year).
     */
    public function copyright(): array;
}