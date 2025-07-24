<?php

declare(strict_types=1);

namespace Rendering\Infrastructure\Building\Renderable;

use Rendering\Infrastructure\Contract\Factory\ValueObject\RenderableDataFactoryInterface;
use InvalidArgumentException;
use Rendering\Domain\Contract\ValueObject\Renderable\RenderableDataInterface;
use Rendering\Domain\ValueObject\Renderable\RenderableData;

class RenderableDataFactory implements RenderableDataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data): RenderableDataInterface
    {
        return new RenderableData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromKeyValue(string $key, mixed $value): RenderableDataInterface
    {
        if (trim($key) === '') {
            throw new InvalidArgumentException('Key cannot be empty.');
        }
        
        return new RenderableData([$key => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function createEmpty(): RenderableDataInterface
    {
        return new RenderableData([]);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromMergedArrays(array $dataArrays): RenderableDataInterface
    {
        if (empty($dataArrays)) {
            return $this->createEmpty();
        }

        $mergedData = [];
        foreach ($dataArrays as $index => $dataArray) {
            if (!is_array($dataArray)) {
                throw new InvalidArgumentException(
                    "Data at index {$index} must be an array, " . gettype($dataArray) . " given."
                );
            }
            $mergedData = array_merge($mergedData, $dataArray);
        }

        return new RenderableData($mergedData);
    }
}