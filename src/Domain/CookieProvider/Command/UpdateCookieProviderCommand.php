<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCookieProviderCommand extends AbstractCommand
{
	/**
	 * @param string $cookieProviderId
	 *
	 * @return static
	 */
	public static function create(string $cookieProviderId): self
	{
		return self::fromParameters([
			'cookie_provider_id' => $cookieProviderId,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function cookieProviderId(): string
	{
		return $this->getParam('cookie_provider_id');
	}

	/**
	 * @return string|NULL
	 */
	public function code(): ?string
	{
		return $this->getParam('code');
	}

	/**
	 * @return string|NULL
	 */
	public function type(): ?string
	{
		return $this->getParam('type');
	}

	/**
	 * @return string|NULL
	 */
	public function name(): ?string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string|NULL
	 */
	public function link(): ?string
	{
		return $this->getParam('link');
	}

	/**
	 * @return array|NULL
	 */
	public function purposes(): ?array
	{
		return $this->getParam('purposes');
	}

	/**
	 * @param string $code
	 *
	 * @return $this
	 */
	public function withCode(string $code): self
	{
		return $this->withParam('code', $code);
	}

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function withType(string $type): self
	{
		return $this->withParam('type', $type);
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
	 * @param string $link
	 *
	 * @return $this
	 */
	public function withLink(string $link): self
	{
		return $this->withParam('link', $link);
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
}
