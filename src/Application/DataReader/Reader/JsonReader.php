<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use JsonException;
use App\Application\DataReader\Row;
use App\Application\DataReader\ArrayRowData;
use App\Application\DataReader\Exception\IoException;
use App\Application\DataReader\Resource\FileResource;
use App\Application\DataReader\Resource\StringResource;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Exception\RowValidationException;

final class JsonReader extends AbstractReader
{
	/**
	 * @param \App\Application\DataReader\Resource\FileResource $resource
	 *
	 * @return static
	 */
	public static function fromFile(FileResource $resource): self
	{
		return new self($resource);
	}

	/**
	 * @param \App\Application\DataReader\Resource\StringResource $resource
	 *
	 * @return static
	 */
	public static function fromString(StringResource $resource): self
	{
		return new self($resource);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function doRead(ErrorCallback $errorCallback): iterable
	{
		try {
			$json = $this->parseJson();
		} catch (IoException|JsonException $e) {
			$errorCallback(ReaderException::invalidResource($e->getMessage()));

			return [];
		}

		if (!is_array($json)) {
			$errorCallback(ReaderException::invalidResource('The JSON root element must be an array.'));

			return [];
		}

		foreach ($json as $index => $row) {
			if (!is_array($row)) {
				$errorCallback(RowValidationException::error((string) $index, sprintf(
					'Row must be an object, %s given.',
					gettype($row)
				)));

				continue;
			}

			yield Row::create((string) $index, ArrayRowData::create($row));
		}
	}

	/**
	 * @return mixed|void
	 * @throws \JsonException
	 */
	private function parseJson()
	{
		if ($this->resource instanceof FileResource) {
			$content = $this->resource->content();
		} else {
			assert($this->resource instanceof StringResource);

			$content = $this->resource->string();
		}

		return json_decode($content, TRUE, 512, JSON_THROW_ON_ERROR);
	}
}
