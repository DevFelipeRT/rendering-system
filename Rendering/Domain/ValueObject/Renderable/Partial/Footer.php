<?php

declare(strict_types=1);

namespace Rendering\Domain\ValueObject\Renderable\Partial;

use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\PartialsCollectionInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;

/**
 * Immutable value object representing a footer partial view component.
 *
 * This class extends PartialView to provide a reusable footer component
 * that encapsulates its template file reference, rendering data (including a copyright notice),
 * and nested partial components. It follows the Value Object pattern, ensuring immutability
 * and data integrity through validation at instantiation time.
 */
final class Footer extends PartialView implements FooterInterface
{
    public readonly string $copyrightOwner;
    public readonly string $copyrightMessage;
    public readonly string $copyrightYear;

    /**
     * Constructor for the Footer component.
     *
     * @param string $templateFile The template file path for this footer.
     * @param RenderableDataInterface|null $data Optional data for the footer template.
     * @param PartialsCollectionInterface|null $partials Optional nested partials collection.
     * @param string $copyrightOwner The copyright owner name.
     * @param string $copyrightMessage The copyright message (default: 'All rights reserved.').
     * @param string|null $copyrightYear The copyright year (default: current year).
     */
    public function __construct(
        string $templateFile,
        ?RenderableDataInterface $data = null,
        ?PartialsCollectionInterface $partials = null,
        string $copyrightOwner = '',
        string $copyrightMessage = 'All rights reserved.',
        ?string $copyrightYear = null
    ) {
        parent::__construct($templateFile, $data, $partials);
        
        $this->copyrightOwner = $copyrightOwner;
        $this->copyrightMessage = $copyrightMessage;
        $this->copyrightYear = $copyrightYear ?? date('Y');
    }

    /**
     * {@inheritdoc}
     */
    public function copyright(): array
    {
        return [
            'owner' => $this->copyrightOwner,
            'message' => $this->copyrightMessage,
            'year' => $this->copyrightYear
        ];
    }
}
