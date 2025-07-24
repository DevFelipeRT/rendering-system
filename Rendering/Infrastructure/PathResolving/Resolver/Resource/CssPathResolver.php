<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver\Resource;

/**
 * Resolves CSS file names into absolute, validated file system paths and web URLs.
 */
final class CssPathResolver extends AbstractResourceResolver
{
    protected const SUBDIRECTORY = 'css';
    protected const FILE_EXTENSION = '.css';

}