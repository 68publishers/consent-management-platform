<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutCrawlerSettingsCommand extends AbstractCommand
{
	public static function create(string $hostUrl, string $username, string $password, ?string $callbackUriToken): self
	{
		return self::fromParameters([
			'host_url' => $hostUrl,
			'username' => $username,
			'password' => $password,
			'callback_uri_token' => $callbackUriToken,
		]);
	}

	public function hostUrl(): string
	{
		return $this->getParam('host_url');
	}

	public function username(): string
	{
		return $this->getParam('username');
	}

	public function password(): string
	{
		return $this->getParam('password');
	}

	public function callbackUriToken(): string
	{
		return $this->getParam('callback_uri_token');
	}
}
