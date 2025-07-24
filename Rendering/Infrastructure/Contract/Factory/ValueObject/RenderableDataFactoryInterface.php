<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Contract\Factory\ValueObject;

use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;

/**
 * Defines the contract for creating RenderableData instances.
 *
 * This factory handles the creation and validation of RenderableData
 * objects from various input formats and data types.
 */
interface RenderableDataFactoryInterface
{
    /**
     * Creates a RenderableData from an associative array.
     *
     * @param array<string, mixed> $data The data array.
     * @return RenderableDataInterface The created data object.
     * @throws InvalidArgumentException if the input is invalid.
     */
    public function createFromArray(array $data): RenderableDataInterface;

    /**
     * Creates a RenderableData from a single key-value pair.
     *
     * @param string $key The data key.
     * @param mixed $value The data value.
     * @return RenderableDataInterface The created data object.
     * @throws InvalidArgumentException if the key is invalid.
     */
    public function createFromKeyValue(string $key, mixed $value): RenderableDataInterface;

    /**
     * Creates an empty RenderableData.
     *
     * @return RenderableDataInterface The empty data object.
     */
    public function createEmpty(): RenderableDataInterface;

    /**
     * Merges multiple data arrays into a single RenderableData.
     *
     * @param array<array<string, mixed>> $dataArrays Multiple data arrays to merge.
     * @return RenderableDataInterface The merged data object.
     * @throws InvalidArgumentException if any input is invalid.
     */
    public function createFromMergedArrays(array $dataArrays): RenderableDataInterface;
}
