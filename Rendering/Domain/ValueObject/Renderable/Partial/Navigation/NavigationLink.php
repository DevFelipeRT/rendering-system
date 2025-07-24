<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial\Navigation;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkInterface;

/**
 * An immutable Value Object representing a single link within a navigation menu.
 *
 * It encapsulates all properties of a hyperlink, including its destination,
 * display text, and whether it represents the currently active page.
 */
final class NavigationLink implements NavigationLinkInterface
{
    /**
     * @param string $url       The link's destination URL (e.g., '/about').
     * @param string $label     The text to display for the link (e.g., 'About Us').
     * @param bool   $visible   Whether this link should be rendered in the navigation.
     * @param bool   $active    Whether this link represents the current page.
     * @param string $iconClass Optional CSS class for an icon to display alongside the link.
     */
    public function __construct(
        private readonly string $url,
        private readonly string $label,
        private readonly bool   $visible = false,
        private readonly bool   $active = false,
        private readonly string $iconClass = ''
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function active(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function visible(): bool
    {
        return $this->visible;
    }

    /**
     * {@inheritdoc}
     */
    public function iconClass(): string
    {
        return $this->iconClass;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'url'      => $this->url(),
            'label'    => $this->label(),
            'active'   => $this->active()
        ];
    }
}