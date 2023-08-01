<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\Exception\RowValidationException;
use App\Application\DataProcessor\Read\Resource\ArrayResource;
use App\Application\DataProcessor\Row;

final class ArrayReader extends AbstractReader
{
    public static function fromArray(ArrayResource $resource): self
    {
        return new self($resource);
    }

    protected function doRead(ErrorCallback $errorCallback): iterable
    {
        $resource = $this->resource;
        assert($resource instanceof ArrayResource);

        foreach ($resource->data() as $index => $row) {
            if (!is_array($row)) {
                $errorCallback(RowValidationException::error((string) $index, sprintf(
                    'Row must be an array, %s given.',
                    gettype($row),
                )));

                continue;
            }

            yield Row::create((string) $index, ArrayRowData::create($row));
        }
    }
}
