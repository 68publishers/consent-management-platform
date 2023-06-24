<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCookieCommand extends AbstractCommand
{
	/**
	 * @param array<string> $purposes
	 */
	public static function create(string $categoryId, string $cookieProviderId, string $name, string $domain, string $processingTime, bool $active, array $purposes, ?string $cookieId = NULL): self
	{
		return self::fromParameters([
			'category_id' => $categoryId,
			'cookie_provider_id' => $cookieProviderId,
			'name' => $name,
			'domain' => $domain,
			'processing_time' => $processingTime,
			'active' => $active,
			'purposes' => $purposes,
			'cookie_id' => $cookieId,
		]);
	}

	public function categoryId(): string
	{
		return $this->getParam('category_id');
	}

	public function cookieProviderId(): string
	{
		return $this->getParam('cookie_provider_id');
	}

	public function name(): string
	{
		return $this->getParam('name');
	}

	public function domain(): string
	{
		return $this->getParam('domain');
	}

	public function processingTime(): string
	{
		return $this->getParam('processing_time');
	}

	/**
	 * @return array<string>
	 */
	public function purposes(): array
	{
		return $this->getParam('purposes');
	}

	public function active(): bool
	{
		return $this->getParam('active');
	}

	public function cookieId(): ?string
	{
		return $this->getParam('cookie_id');
	}
}
