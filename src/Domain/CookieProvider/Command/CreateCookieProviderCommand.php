<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCookieProviderCommand extends AbstractCommand
{
	/**
	 * @param string      $code
	 * @param string      $type
	 * @param string      $name
	 * @param string      $link
	 * @param string[]    $purposes
	 * @param bool        $private
	 * @param string|NULL $cookieProviderId
	 *
	 * @return static
	 */
	public static function create(string $code, string $type, string $name, string $link, array $purposes, bool $private, ?string $cookieProviderId = NULL): self
	{
		return self::fromParameters([
			'code' => $code,
			'type' => $type,
			'name' => $name,
			'link' => $link,
			'purposes' => $purposes,
			'private' => $private,
			'cookie_provider_id' => $cookieProviderId,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}

	/**
	 * @return string
	 */
	public function type(): string
	{
		return $this->getParam('type');
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
	public function link(): string
	{
		return $this->getParam('link');
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
	public function private(): bool
	{
		return $this->getParam('private');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieProviderId(): ?string
	{
		return $this->getParam('cookie_provider_id');
	}
}
