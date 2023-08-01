<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Exception\RowValidationException;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Row;
use Throwable;

final class PhpReader extends AbstractReader
{
    public static function fromFile(FileResource $resource): self
    {
        return new self($resource);
    }

    protected function doRead(ErrorCallback $errorCallback): iterable
    {
        $resource = $this->resource;
        assert($resource instanceof FileResource);

        if (!$resource->exists()) {
            $errorCallback(ReaderException::invalidResource(sprintf(
                'File %s not found.',
                $resource->filename(),
            )));

            return [];
        }

        if ('php' !== $resource->extension()) {
            $errorCallback(ReaderException::invalidResource('PHP file must be provided.'));

            return [];
        }

        try {
            $data = include $resource->filename();
        } catch (Throwable $e) {
            $errorCallback(ReaderException::invalidResource($e->getMessage()));

            return [];
        }

        if (!is_array($data)) {
            $errorCallback(ReaderException::invalidResource('The file must return an array.'));

            return [];
        }

        foreach ($data as $index => $row) {
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
