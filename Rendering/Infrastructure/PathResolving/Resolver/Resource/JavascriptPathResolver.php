<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\PathResolving\Resolver\Resource;

/**
 * Resolves JavaScript file names into absolute, validated file system paths and web URLs.
 */
final class JavascriptPathResolver extends AbstractResourceResolver
{
    protected const SUBDIRECTORY = 'js';
    protected const FILE_EXTENSION = '.js';
}
