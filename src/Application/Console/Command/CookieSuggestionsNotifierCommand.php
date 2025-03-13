<?php

declare(strict_types=1);

namespace App\Application\Console\Command;

use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\SuggestionInterface;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\Mail\Address;
use App\Application\Mail\Command\SendMailCommand;
use App\Application\Mail\Message;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\ValueObject\NotificationType;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery;
use App\ReadModel\User\NotificationReceiverView;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class CookieSuggestionsNotifierCommand extends Command
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly CookieSuggestionsStoreInterface $cookieSuggestionsStore,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('cmp:cookie-suggestions-notifier')
            ->setDescription('Sends notifications about unresolved cookie suggestions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $projects = [];

        $mapSuggestions = static fn (array $suggestions): array =>
            array_map(static fn (SuggestionInterface $suggestion): array => [
                'name' => $suggestion->getSuggestionName(),
                'domain' => $suggestion->getSuggestionDomain(),
                'lastFoundedAt' => null !== $suggestion->getLatestOccurrence() ? $suggestion->getLatestOccurrence()->lastFoundAt->format('j.n.Y H:i:s') : null,
            ], $suggestions);

        $projectQuery = FindProjectSelectOptionsQuery::all()
            ->withActiveOnly(true);

        foreach ($this->queryBus->dispatch($projectQuery) as $projectSelectOptionView) {
            assert($projectSelectOptionView instanceof ProjectSelectOptionView);

            $projectId = $projectSelectOptionView->id->toString();
            $suggestions = $this->cookieSuggestionsStore->resolveCookieSuggestions($projectId);
            $missingCookieSuggestions = $suggestions->getSuggestionsByType(MissingCookieSuggestion::class);
            $unassociatedCookieSuggestions = $suggestions->getSuggestionsByType(UnassociatedCookieSuggestion::class);
            $problematicCookieSuggestions = $suggestions->getSuggestionsByType(ProblematicCookieSuggestion::class);

            if (0 >= array_sum([count($missingCookieSuggestions), count($unassociatedCookieSuggestions), count($problematicCookieSuggestions)])) {
                continue;
            }

            $projects[$projectId] = [
                'id' => $projectId,
                'name' => $projectSelectOptionView->name->value(),
                'missingCookieSuggestions' => $mapSuggestions($missingCookieSuggestions),
                'unassociatedCookieSuggestions' => $mapSuggestions($unassociatedCookieSuggestions),
                'problematicCookieSuggestions' => $mapSuggestions($problematicCookieSuggestions),
            ];
        }

        if (0 < count($projects)) {
            $this->processProjects($projects, $logger);
        }

        $output->writeln('OK');

        return 0;
    }

    protected function processProjects(array $projects, LoggerInterface $logger): void
    {
        foreach ($this->queryBus->dispatch(FindNotificationReceiversByTypeAndProjectIdsQuery::create(NotificationType::COOKIE_SUGGESTIONS)) as $batch) {
            assert($batch instanceof Batch);

            foreach ($batch->results() as $notificationReceiverView) {
                assert($notificationReceiverView instanceof NotificationReceiverView);

                $this->notify($notificationReceiverView, $projects, $logger);
            }
        }
    }

    private function notify(NotificationReceiverView $notificationReceiverView, array $projects, LoggerInterface $logger): void
    {
        $userProjects = [];

        foreach ($projects as $project) {
            foreach ($notificationReceiverView->projectIds as $projectId) {
                if ($projectId->equals(ProjectId::fromString($project['id']))) {
                    $userProjects[] = $project;

                    continue 2;
                }
            }
        }

        if (0 >= count($userProjects)) {
            $logger->info(sprintf(
                'The user %s has no associated projects, skipping...',
                $notificationReceiverView->emailAddress->value(),
            ));

            return;
        }

        $logger->info(sprintf(
            'Sending notification to %s ...',
            $notificationReceiverView->emailAddress->value(),
        ));

        $message = Message::create('~cookie_suggestions.latte', $notificationReceiverView->profileLocale->value())
            ->withTo(Address::create($notificationReceiverView->emailAddress->value(), $notificationReceiverView->name->name()))
            ->withArguments([
                'projects' => $userProjects,
            ]);

        $this->commandBus->dispatch(SendMailCommand::create($message));
    }
}
