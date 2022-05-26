<?php

declare(strict_types=1);

namespace App\Domain\Category\CommandHandler;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepositoryInterface;
use App\Domain\Category\CheckCodeUniquenessInterface;
use App\Domain\Category\Command\CreateCategoryCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class CreateCategoryCommandHandler implements CommandHandlerInterface
{
	private CategoryRepositoryInterface $categoryRepository;

	private CheckCodeUniquenessInterface $checkCodeUniqueness;

	/**
	 * @param \App\Domain\Category\CategoryRepositoryInterface  $categoryRepository
	 * @param \App\Domain\Category\CheckCodeUniquenessInterface $checkCodeUniqueness
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepository, CheckCodeUniquenessInterface $checkCodeUniqueness)
	{
		$this->categoryRepository = $categoryRepository;
		$this->checkCodeUniqueness = $checkCodeUniqueness;
	}

	/**
	 * @param \App\Domain\Category\Command\CreateCategoryCommand $command
	 *
	 * @return void
	 */
	public function __invoke(CreateCategoryCommand $command): void
	{
		$category = Category::create($command, $this->checkCodeUniqueness);

		$this->categoryRepository->save($category);
	}
}
