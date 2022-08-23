<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Write\Helper\FilePutContents;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

final class JsonWriter extends AbstractWriter
{
	public const OPTION_PRETTY = 'pretty';
	public const OPTION_UNESCAPED_UNICODE = 'unescaped_unicode';

	private array $data = [];

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
	 */
	protected function prepare(): void
	{
		$this->data = [];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processRow(RowInterface $row, DestinationInterface $destination): DestinationInterface
	{
		$this->data[] = $row->data()->toArray();

		return $destination;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \JsonException
	 */
	protected function finish(DestinationInterface $destination): DestinationInterface
	{
		$flags = 0;

		if (TRUE === ($destination->options()[self::OPTION_PRETTY] ?? FALSE)) {
			$flags |= JSON_PRETTY_PRINT;
		}

		if (TRUE === ($destination->options()[self::OPTION_UNESCAPED_UNICODE] ?? FALSE)) {
			$flags |= JSON_UNESCAPED_UNICODE;
		}

		$json = json_encode($this->data, $flags | JSON_THROW_ON_ERROR);
		$this->data = [];

		if ($destination instanceof StringDestination) {
			return $destination->append($json);
		}

		assert($destination instanceof FileDestination);

		FilePutContents::put($destination, $json);

		return $destination;
	}
}
