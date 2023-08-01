<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class FailImportCommand extends AbstractCommand
{
    public static function create(string $id, int $imported, int $failed, int $warned, string $output): self
    {
        return self::fromParameters([
            'id' => $id,
            'imported' => $imported,
            'failed' => $failed,
            'warned' => $warned,
            'output' => $output,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }

    public function imported(): int
    {
        return $this->getParam('imported');
    }

    public function failed(): int
    {
        return $this->getParam('failed');
    }

    public function warned(): int
    {
        return $this->getParam('warned');
    }

    public function output(): string
    {
        return $this->getParam('output');
    }
}
