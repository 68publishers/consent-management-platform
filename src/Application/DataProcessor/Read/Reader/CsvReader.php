<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use League\Csv\Info;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Application\DataProcessor\Row;
use League\Csv\Exception as LeagueException;
use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\Context\Context;
use App\Application\DataProcessor\Helper\FlatResource;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Read\Resource\StringResource;

final class CsvReader extends AbstractReader
{
	public const OPTION_DELIMITER = 'delimiter';
	public const OPTION_ENCLOSURE = 'enclosure';
	public const OPTION_ESCAPE = 'escape';
	public const OPTION_HAS_HEADER = 'has_header';

	/**
	 * @param \App\Application\DataProcessor\Read\Resource\FileResource $resource
	 *
	 * @return static
	 */
	public static function fromFile(FileResource $resource): self
	{
		return new self($resource);
	}

	/**
	 * @param \App\Application\DataProcessor\Read\Resource\StringResource $resource
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
			yield Row::create((string) $index, ArrayRowData::create(FlatResource::toMultidimensionalArray($row)));
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
			$encoding = mb_detect_encoding($reader->toString());
		} else {
			assert($resource instanceof StringResource);

			$reader = Reader::createFromString($resource->string());
			$encoding = mb_detect_encoding($resource->string());
		}

		if (is_string($options[self::OPTION_DELIMITER] ?? NULL)) {
			$reader->setDelimiter($options[self::OPTION_DELIMITER]);
		} else {
			$stats = Info::getDelimiterStats($reader, [',', ';', "\t", '|']);
			arsort($stats);

			$reader->setDelimiter(array_key_first($stats));
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

		// probably windows
		if (FALSE === $encoding) {
			$reader->addStreamFilter('convert.iconv.WINDOWS-1250/UTF-8//TRANSLIT//IGNORE');
		}

		return $reader;
	}
}