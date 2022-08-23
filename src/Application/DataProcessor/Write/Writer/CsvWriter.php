<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use League\Csv\Writer;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Helper\FlatResource;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use App\Application\DataProcessor\Description\ListDescriptor;
use App\Application\DataProcessor\Write\Helper\FilePutContents;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

final class CsvWriter extends AbstractWriter
{
	public const OPTION_DELIMITER = 'delimiter';
	public const OPTION_ENCLOSURE = 'enclosure';
	public const OPTION_ESCAPE = 'escape';
	public const INCLUDE_BOM = 'include_bom';

	private ?Writer $writer = NULL;

	private array $headers = [];

	private array $rows = [];

	private array $pathInfos = [];

	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface  $resource
	 * @param \App\Application\DataProcessor\Write\Destination\FileDestination $destination
	 *
	 * @return static
	 */
	public static function fromFile(ResourceInterface $resource, FileDestination $destination): self
	{
		return new self($resource, $destination);
	}

	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface    $resource
	 * @param \App\Application\DataProcessor\Write\Destination\StringDestination $destination
	 *
	 * @return static
	 */
	public static function fromString(ResourceInterface $resource, StringDestination $destination): self
	{
		return new self($resource, $destination);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \League\Csv\InvalidArgument
	 */
	protected function prepare(): void
	{
		$this->writer = $writer = Writer::createFromString();
		$this->headers = $this->rows = $this->pathInfos = [];
		$options = $this->destination->options();

		if (isset($options[self::OPTION_DELIMITER])) {
			$writer->setDelimiter($options[self::OPTION_DELIMITER]);
		}

		if (isset($options[self::OPTION_ENCLOSURE])) {
			$writer->setEnclosure($options[self::OPTION_ENCLOSURE]);
		}

		if (isset($options[self::OPTION_ESCAPE])) {
			$writer->setEscape($options[self::OPTION_ESCAPE]);
		}

		if (isset($options[self::INCLUDE_BOM])) {
			$writer->setOutputBOM($writer::BOM_UTF8);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processRow(RowInterface $row, DestinationInterface $destination): DestinationInterface
	{
		$flatten = FlatResource::toFlattArray($row->data()->toArray());
		$data = [];

		foreach ($flatten as $k => $v) {
			if (is_array($v)) {
				$v = implode(',', $v);
			}

			$pathInfo = $this->getPathInfo($k);

			if (NULL === $pathInfo) {
				$this->headers[] = $k;
				$data[$k] = $v;

				continue;
			}

			if (!$pathInfo->found) {
				continue;
			}

			if ($pathInfo->isFinal || $pathInfo->descriptor instanceof ListDescriptor) {
				$this->headers[] = $k;
				$data[$k] = $v;
			}
		}

		$this->rows[] = $data;

		return $destination;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \League\Csv\Exception
	 */
	protected function finish(DestinationInterface $destination): DestinationInterface
	{
		$headers = array_unique($this->headers);
		$defaults = array_fill_keys($headers, '');

		$this->writer->insertOne($headers);

		foreach ($this->rows as $row) {
			$this->writer->insertOne(array_merge($defaults, $row));
		}

		$content = $this->writer->toString();
		$this->writer = NULL;
		$this->headers = $this->rows = $this->pathInfos = [];

		if ($destination instanceof StringDestination) {
			return $destination->append($content);
		}

		assert($destination instanceof FileDestination);

		FilePutContents::put($destination, $content);

		return $destination;
	}

	/**
	 * @param string $path
	 *
	 * @return \App\Application\DataProcessor\Description\Path\PathInfo|NULL
	 */
	private function getPathInfo(string $path): ?PathInfo
	{
		return $this->pathInfos[$path] ?? ($this->pathInfos[$path] = NULL !== $this->resource->descriptor() ? $this->resource->descriptor()->pathInfo(Path::fromString($path)) : NULL);
	}
}
