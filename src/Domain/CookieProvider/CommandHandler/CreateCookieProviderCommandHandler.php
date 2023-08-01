<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\CommandHandler;

use App\Domain\CookieProvider\CheckCodeUniquenessInterface;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\CookieProvider\CookieProviderRepositoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class CreateCookieProviderCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly CookieProviderRepositoryInterface $cookieProviderRepository,
        private readonly CheckCodeUniquenessInterface $checkCodeUniqueness,
    ) {}

    public function __invoke(CreateCookieProviderCommand $command): void
    {
        $category = CookieProvider::create($command, $this->checkCodeUniqueness);

        $this->cookieProviderRepository->save($category);
    }
}
