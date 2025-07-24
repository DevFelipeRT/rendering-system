<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable\Partial\Builder;

use Rendering\Domain\Contract\Service\Building\Partial\FooterBuilderInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\Partial\FooterInterface;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\ValueObject\Renderable\Partial\Footer;
use Rendering\Infrastructure\Building\Renderable\Partial\Factory\PartialFactory;
use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;

/**
 * Implements the Builder pattern to assemble a complete Footer object.
 */
final class FooterBuilder extends PartialBuilder implements FooterBuilderInterface
{
    private const  DEFAULT_TEMPLATE  = 'partial/footer.phtml';
    private string $copyrightOwner   = '';
    private string $copyrightMessage = 'All rights reserved.';

    /**
     * Constructor to initialize the FooterBuilder with necessary factories.
     * Sets the default template file for the footer.
     * This builder allows setting a copyright notice and message, which will be included in the footer view.
     * Template files can be overridden by calling setTemplateFile().
     *
     * @param PartialFactory $partialFactory   Factory to create partial views.
     * @param RenderableDataFactoryInterface $dataFactory Factory to create renderable data.
     * @param string|null    $copyrightOwner   The owner of the copyright notice.
     * @param string|null    $copyrightMessage The message for the copyright notice.
     */
    public function __construct(
        PartialFactory $partialFactory, 
        RenderableDataFactoryInterface $dataFactory,
        ?string $copyrightOwner,
        ?string $copyrightMessage
    ) {
        parent::__construct($partialFactory, $dataFactory);
        $this->initializeCopyrightNotice($copyrightOwner, $copyrightMessage);
        $this->initializeTemplateFile(self::DEFAULT_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright(string $owner, string $message = 'All rights reserved.'): self
    {
        $this->copyrightOwner = $owner;
        $this->copyrightMessage = $message;
        $this->isConfigured = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): FooterInterface
    {
        $this->checkReadyState();
        return new Footer(
            $this->templateFile,
            $this->buildFooterData(),
            $this->buildPartialsCollection($this->partials)
        );
    }

    /**
     * Builds the data object for the footer.
     *
     * Merges the current data with the copyright notice and returns a RenderableDataInterface.
     *
     * @return RenderableDataInterface The data object for the footer.
     */
    private function buildFooterData(): RenderableDataInterface
    {
        $data = array_merge(
            $this->data,
            ['copyrightNotice' => $this->buildCopyrightNotice()]
        );
        return $this->buildDataFromArray($data);
    }

    private function initializeCopyrightNotice(string $copyrightOwner = '', string $copyrightMessage = 'All rights reserved.'): void
    {
        if (!empty($copyrightOwner) || !empty($copyrightMessage)) {
            $this->copyrightOwner = $copyrightOwner;
            $this->copyrightMessage = $copyrightMessage;
        }
    }

    /**
     * Builds the copyright notice string.
     *
     * Constructs a string containing the current year, the copyright owner, and the copyright message.
     *
     * @return string The formatted copyright notice.
     */
    private function buildCopyrightNotice(): string
    {
        return '© ' . date('Y') . " {$this->copyrightOwner}. {$this->copyrightMessage}";
    }
}
