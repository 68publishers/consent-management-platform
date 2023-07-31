<?php

declare(strict_types=1);

namespace App\Application\Console\Command;

use Psr\Log\LoggerInterface;
use App\Application\Mail\Address;
use App\Application\Mail\Message;
use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Component\Console\Command\Command;
use App\ReadModel\User\NotificationReceiverView;
use App\Application\Mail\Command\SendMailCommand;
use App\Domain\User\ValueObject\NotificationType;
use App\ReadModel\Project\ProjectSelectOptionView;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use App\Application\CookieSuggestion\Suggestion\SuggestionInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;

final class CookieSuggestionsNotifierCommand extends Command
{
	private QueryBusInterface $queryBus;

	private CommandBusInterface $commandBus;

	private CookieSuggestionsStoreInterface $cookieSuggestionsStore;

	public function __construct(QueryBusInterface $queryBus, CommandBusInterface $commandBus, CookieSuggestionsStoreInterface $cookieSuggestionsStore)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
		$this->cookieSuggestionsStore = $cookieSuggestionsStore;
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
				'lastFoundedAt' => NULL !== $suggestion->getLatestOccurrence() ? $suggestion->getLatestOccurrence()->lastFoundAt->format('j.n.Y H:i:s') : NULL,
			], $suggestions);

		foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
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
				$notificationReceiverView->emailAddress->value()
			));

			return;
		}

		$logger->info(sprintf(
			'Sending notification to %s ...',
			$notificationReceiverView->emailAddress->value()
		));

		$message = Message::create('~cookie_suggestions.latte', $notificationReceiverView->profileLocale->value())
			->withTo(Address::create($notificationReceiverView->emailAddress->value(), $notificationReceiverView->name->name()))
			->withArguments([
				'projects' => $userProjects,
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message));
	}
}
