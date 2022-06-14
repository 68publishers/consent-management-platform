<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCookieCommand extends AbstractCommand
{
	/**
	 * @param string      $categoryId
	 * @param string      $cookieProviderId
	 * @param string      $name
	 * @param array       $purposes
	 * @param array       $processingTimes
	 * @param string|NULL $cookieId
	 *
	 * @return static
	 */
	public static function create(string $categoryId, string $cookieProviderId, string $name, array $purposes, array $processingTimes, ?string $cookieId = NULL): self
	{
		return self::fromParameters([
			'category_id' => $categoryId,
			'cookie_provider_id' => $cookieProviderId,
			'name' => $name,
			'purposes' => $purposes,
			'processing_times' => $processingTimes,
			'cookie_id' => $cookieId,
		]);
	}

	/**
	 * @return string
	 */
	public function categoryId(): string
	{
		return $this->getParam('category_id');
	}

	/**
	 * @return string
	 */
	public function cookieProviderId(): string
	{
		return $this->getParam('cookie_provider_id');
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string[]
	 */
	public function purposes(): array
	{
		return $this->getParam('purposes');
	}

	/**
	 * @return string[]
	 */
	public function processingTimes(): array
	{
		return $this->getParam('processing_times');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieId(): ?string
	{
		return $this->getParam('cookie_id');
	}
}
