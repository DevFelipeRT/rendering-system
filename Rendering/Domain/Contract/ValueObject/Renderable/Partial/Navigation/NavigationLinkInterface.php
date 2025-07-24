<?php 

declare(strict_types=1);

namespace Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation;

/**
 * Defines the contract for a single navigation link within a navigation menu.
 *
 * This interface specifies the methods required to retrieve the URL, label,
 * and active state of a navigation link, allowing for consistent rendering
 * and interaction within navigation components.
 */
interface NavigationLinkInterface
{
    /**
     * Returns the URL for the navigation link.
     *
     * @return string The URL to navigate to when the link is clicked.
     */
    public function url(): string;

    /**
     * Returns the label for the navigation link.
     *
     * @return string The text displayed for the link.
     */
    public function label(): string;

    /**
     * Checks if this link should be visible in the navigation.
     *
     * @return bool True if the link should be rendered, false otherwise.
     */
    public function visible(): bool;

    /**
     * Checks if this link is the currently active page.
     *
     * @return bool True if this link represents the current page, false otherwise.
     */
    public function active(): bool;

    /**
     * Returns the CSS class for an icon associated with the link.
     *
     * @return string The CSS class name for the icon, if any.
     */
    public function iconClass(): string;

    /**
     * Converts the navigation link to an array representation.
     *
     * This method is useful for rendering the link in templates or passing
     * it to other components that require an array format.
     *
     * @return array<string, mixed> An associative array containing the link's properties.
     */
    public function toArray(): array;
}