<?php

declare(strict_types=1);

namespace App\Application\Mail\Command;

use App\Application\Mail\Message;
use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class SendMailCommand extends AbstractCommand
{
	/**
	 * @param \App\Application\Mail\Message $message
	 *
	 * @return static
	 */
	public static function create(Message $message): self
	{
		return self::fromParameters([
			'message' => $message,
		]);
	}

	/**
	 * @return \App\Application\Mail\Message
	 */
	public function message(): Message
	{
		return $this->getParam('message');
	}
}
