<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StartImportCommand extends AbstractCommand
{
    public static function create(string $id, string $name, ?string $authorId = null): self
    {
        return self::fromParameters([
            'id' => $id,
            'name' => $name,
            'author_id' => $authorId,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }

    public function name(): string
    {
        return $this->getParam('name');
    }

    public function authorId(): ?string
    {
        return $this->getParam('author_id');
    }
}
