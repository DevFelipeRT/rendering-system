<?php

declare(strict_types=1);

namespace Rendering\Domain\Contract\Service\Building\Exception;

/**
 * Marker interface for all builder-related exceptions.
 *
 * This interface can be used to catch all exceptions thrown by builders,
 * such as when a builder is not in a ready state.
 */
interface BuilderExceptionInterface extends \Throwable
{
}
