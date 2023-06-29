<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use Nette\Utils\Validators;
use App\Domain\GlobalSettings\Exception\InvalidUrlException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;

final class CrawlerSettings extends AbstractArrayValueObject
{
	public static function fromValues(
		bool $enabled,
		?string $hostUrl,
		?string $username,
		?string $password,
		?string $callbackUriToken
	): self {
		if (NULL !== $hostUrl && !Validators::isUrl($hostUrl)) {
			throw InvalidUrlException::create($hostUrl);
		}

		return self::fromArray([
			'enabled' => $enabled,
			'host_url' => $hostUrl,
			'username' => $username,
			'password' => $password,
			'callback_uri_token' => $callbackUriToken,
		]);
	}

	public function enabled(): bool
	{
		return $this->get('enabled') ?? FALSE;
	}

	public function hostUrl(): ?string
	{
		return $this->get('host_url');
	}

	public function username(): ?string
	{
		return $this->get('username');
	}

	public function password(): ?string
	{
		return $this->get('password');
	}

	public function callbackUriToken(): ?string
	{
		return $this->get('callback_uri_token');
	}
}
