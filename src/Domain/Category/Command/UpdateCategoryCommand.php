<?php

declare(strict_types=1);

namespace App\Domain\Category\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCategoryCommand extends AbstractCommand
{
    /**
     * @return static
     */
    public static function create(string $categoryId): self
    {
        return self::fromParameters([
            'category_id' => $categoryId,
        ]);
    }

    public function categoryId(): string
    {
        return $this->getParam('category_id');
    }

    public function code(): ?string
    {
        return $this->getParam('code');
    }

    public function names(): ?array
    {
        return $this->getParam('names');
    }

    public function active(): ?bool
    {
        return $this->getParam('active');
    }

    public function necessary(): ?bool
    {
        return $this->getParam('necessary');
    }

    /**
     * @return $this
     */
    public function withCode(string $code): self
    {
        return $this->withParam('code', $code);
    }

    /**
     * @return $this
     */
    public function withNames(array $names): self
    {
        return $this->withParam('names', $names);
    }

    /**
     * @return $this
     */
    public function withActive(bool $active): self
    {
        return $this->withParam('active', $active);
    }

    /**
     * @return $this
     */
    public function withNecessary(bool $necessary): self
    {
        return $this->withParam('necessary', $necessary);
    }
}
