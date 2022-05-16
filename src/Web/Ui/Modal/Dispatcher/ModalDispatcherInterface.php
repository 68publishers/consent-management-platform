<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher;

use JsonSerializable;

use Nette\ComponentModel\IComponent;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareInterface;

interface ModalDispatcherInterface extends JsonSerializable, EventDispatcherAwareInterface
{
	public const PARAMS_ON_OPEN = 'params_on_open';
	public const REMOVE_PARAMS_ON_CLOSE = 'remove_params_on_close';

	/**
	 * @param \Nette\ComponentModel\IComponent $modal
	 * @param array                            $metadata
	 *
	 * @return void
	 */
	public function dispatch(IComponent $modal, array $metadata = []): void;

	/**
	 * @param array $componentNames
	 *
	 * @return void
	 */
	public function close(array $componentNames = []): void;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array;
}
