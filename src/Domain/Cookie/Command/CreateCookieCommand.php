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
	 * @param string      $processingTime
	 * @param bool        $active
	 * @param array       $purposes
	 * @param string|NULL $cookieId
	 *
	 * @return static
	 */
	public static function create(string $categoryId, string $cookieProviderId, string $name, string $processingTime, bool $active, array $purposes, ?string $cookieId = NULL): self
	{
		return self::fromParameters([
			'category_id' => $categoryId,
			'cookie_provider_id' => $cookieProviderId,
			'name' => $name,
			'processing_time' => $processingTime,
			'active' => $active,
			'purposes' => $purposes,
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
	 * @return string
	 */
	public function processingTime(): string
	{
		return $this->getParam('processing_time');
	}

	/**
	 * @return string[]
	 */
	public function purposes(): array
	{
		return $this->getParam('purposes');
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->getParam('active');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieId(): ?string
	{
		return $this->getParam('cookie_id');
	}
}
