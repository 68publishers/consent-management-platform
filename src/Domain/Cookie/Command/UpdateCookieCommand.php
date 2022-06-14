<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCookieCommand extends AbstractCommand
{
	/**
	 * @param string $cookieId
	 *
	 * @return static
	 */
	public static function create(string $cookieId): self
	{
		return self::fromParameters([
			'cookie_id' => $cookieId,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function cookieId(): string
	{
		return $this->getParam('cookie_id');
	}

	/**
	 * @return string|NULL
	 */
	public function categoryId(): ?string
	{
		return $this->getParam('category_id');
	}

	/**
	 * @return string|NULL
	 */
	public function name(): ?string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string[]|NULL
	 */
	public function purposes(): ?array
	{
		return $this->getParam('purposes');
	}

	/**
	 * @return string|NULL
	 */
	public function processingTime(): ?string
	{
		return $this->getParam('processing_time');
	}

	/**
	 * @param string $categoryId
	 *
	 * @return $this
	 */
	public function withCategoryId(string $categoryId): self
	{
		return $this->withParam('category_id', $categoryId);
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function withName(string $name): self
	{
		return $this->withParam('name', $name);
	}

	/**
	 * @param string[] $purposes
	 *
	 * @return $this
	 */
	public function withPurposes(array $purposes): self
	{
		return $this->withParam('purposes', $purposes);
	}

	/**
	 * @param string $processingTime
	 *
	 * @return $this
	 */
	public function withProcessingTime(string $processingTime): self
	{
		return $this->withParam('processing_time', $processingTime);
	}
}
