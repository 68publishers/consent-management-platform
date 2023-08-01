<?php

declare(strict_types=1);

namespace App\Application\Mail\Command;

use App\Application\Mail\Message;
use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class SendMailCommand extends AbstractCommand
{
    public static function create(Message $message): self
    {
        return self::fromParameters([
            'message' => $message,
        ]);
    }

    public function message(): Message
    {
        return $this->getParam('message');
    }
}
