<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial\Factory;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\Navigation\NavigationLinkInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\Navigation\NavigationLink;
use Rendering\Domain\ValueObject\Renderable\Partial\Navigation\NavigationLinkCollection;

final class NavigationLinkFactory
{
    /**
     * Creates a new NavigationLink object with the given label, URL, and optional attributes.
     */
    public function createNavigationLink(
        string $label,
        string $url,
        bool $visible = true,
        bool $active = false,
        string $iconClass = ''
    ): NavigationLinkInterface {
        if (empty($label) || empty($url)) {
            throw new InvalidArgumentException('Label and URL cannot be empty.');
        }

        return new NavigationLink($url, $label, $visible, $active, $iconClass);
    }

    /**
     * Creates a NavigationLink object from an associative array.
     */
    public function createNavigationLinkFromArray(array $linkData): NavigationLinkInterface
    {
        if (!isset($linkData['label'], $linkData['url'])) {
            throw new InvalidArgumentException('Each link array must have "label" and "url" keys.');
        }

        return $this->createNavigationLink(
            $linkData['label'],
            $linkData['url'],
            $linkData['visible'] ?? true,
            $linkData['active'] ?? false,
            $linkData['iconClass'] ?? ''
        );
    }

    /**
     * Creates a collection of NavigationLink objects from an array of links.
     */
    public function createNavigationLinkCollection(array $links): NavigationLinkCollectionInterface
    {
        $navigationLinks = [];
        foreach ($links as $linkData) {
            if ($linkData instanceof NavigationLinkInterface) {
                $navigationLinks[] = $linkData;
                continue;
            }

            // Ensure the item is an array before trying to process it.
            if (!is_array($linkData)) {
                throw new InvalidArgumentException(
                    'Each link must be an instance of NavigationLinkInterface or an associative array.'
                );
            }
            
            // The createNavigationLinkFromArray method will handle validation of keys.
            $navigationLinks[] = $this->createNavigationLinkFromArray($linkData);
        }
        return new NavigationLinkCollection($navigationLinks);
    }
}
