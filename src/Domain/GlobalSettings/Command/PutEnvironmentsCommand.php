<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutEnvironmentsCommand extends AbstractCommand
{
    /**
     * @param array<int, Environment> $environments
     */
    public static function create(array $environments): self
    {
        return self::fromParameters([
            'environments' => $environments,
        ]);
    }

    /**
     * @return array<int, Environment>
     */
    public function environments(): array
    {
        return $this->getParam('environments');
    }
}
