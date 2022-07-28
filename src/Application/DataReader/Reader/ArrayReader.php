<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Row;
use App\Application\DataReader\ArrayRowData;
use App\Application\DataReader\Resource\ArrayResource;
use App\Application\DataReader\Exception\RowValidationException;

final class ArrayReader extends AbstractReader
{
	/**
	 * @param \App\Application\DataReader\Resource\ArrayResource $resource
	 *
	 * @return static
	 */
	public static function fromArray(ArrayResource $resource): self
	{
		return new self($resource);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function doRead(ErrorCallback $errorCallback): iterable
	{
		$resource = $this->resource;
		assert($resource instanceof ArrayResource);

		foreach ($resource->data() as $index => $row) {
			if (!is_array($row)) {
				$errorCallback(RowValidationException::error((string) $index, sprintf(
					'Row must be an array, %s given.',
					gettype($row)
				)));

				continue;
			}

			yield Row::create((string) $index, ArrayRowData::create($row));
		}
	}
}
