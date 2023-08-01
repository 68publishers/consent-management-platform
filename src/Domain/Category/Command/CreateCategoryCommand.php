<?php

declare(strict_types=1);

namespace App\Domain\Category\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCategoryCommand extends AbstractCommand
{
    public static function create(string $code, array $names, bool $active, bool $necessary, ?string $categoryId = null): self
    {
        return self::fromParameters([
            'code' => $code,
            'names' => $names,
            'active' => $active,
            'necessary' => $necessary,
            'category_id' => $categoryId,
        ]);
    }

    public function code(): string
    {
        return $this->getParam('code');
    }

    public function names(): array
    {
        return $this->getParam('names');
    }

    public function active(): bool
    {
        return $this->getParam('active');
    }

    public function necessary(): bool
    {
        return $this->getParam('necessary');
    }

    public function categoryId(): ?string
    {
        return $this->getParam('category_id');
    }
}
