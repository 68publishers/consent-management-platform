<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use League\Csv\Reader;
use League\Csv\Statement;
use App\Application\DataReader\Row;
use App\Application\DataReader\ArrayRowData;
use League\Csv\Exception as LeagueException;
use App\Application\DataReader\Context\Context;
use App\Application\DataReader\Utils\ResourceUtils;
use App\Application\DataReader\Resource\FileResource;
use App\Application\DataReader\Resource\StringResource;
use App\Application\DataReader\Context\ContextInterface;
use App\Application\DataReader\Exception\ReaderException;

final class CsvReader extends AbstractReader
{
	public const OPTION_DELIMITER = 'delimiter';
	public const OPTION_ENCLOSURE = 'enclosure';
	public const OPTION_ESCAPE = 'escape';
	public const OPTION_HAS_HEADER = 'has_header';

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
			$reader = $this->createReader();
			$rows = Statement::create()->process($reader);
		} catch (LeagueException $e) {
			$errorCallback(ReaderException::invalidResource($e->getMessage()));

			return [];
		}

		foreach ($rows as $index => $row) {
			yield Row::create((string) $index, ArrayRowData::create(ResourceUtils::toMultidimensionalArray($row)));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function createContext(): ContextInterface
	{
		return Context::default([
			ContextInterface::WEAK_TYPES => TRUE,
		]);
	}

	/**
	 * @return \League\Csv\Reader
	 * @throws \League\Csv\Exception
	 * @throws \League\Csv\InvalidArgument
	 */
	private function createReader(): Reader
	{
		$resource = $this->resource;
		$options = $resource->options();

		if ($resource instanceof FileResource) {
			$reader = Reader::createFromPath($resource->filename());
		} else {
			assert($resource instanceof StringResource);

			$reader = Reader::createFromString($resource->string());
		}

		if (is_string($options[self::OPTION_DELIMITER] ?? NULL)) {
			$reader->setDelimiter($options[self::OPTION_DELIMITER]);
		}

		if (is_string($options[self::OPTION_ENCLOSURE] ?? NULL)) {
			$reader->setEnclosure($options[self::OPTION_ENCLOSURE]);
		}

		if (TRUE === (isset($options[self::OPTION_HAS_HEADER])) ?? FALSE) {
			$reader->setHeaderOffset(0);
		}

		if (TRUE === (isset($options[self::OPTION_ESCAPE])) ?? FALSE) {
			$reader->setEscape($options[self::OPTION_ESCAPE]);
		}

		return $reader;
	}
}
