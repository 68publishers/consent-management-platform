<?php

declare(strict_types=1);

namespace App\Domain\Category\CommandHandler;

use App\Domain\Category\CategoryRepositoryInterface;
use App\Domain\Category\Command\DeleteCategoryCommand;
use App\Domain\Category\ValueObject\CategoryId;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class DeleteCategoryCommandHandler implements CommandHandlerInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $category = $this->categoryRepository->get(CategoryId::fromString($command->categoryId()));

        $category->delete();

        $this->categoryRepository->save($category);
    }
}
