<?php

declare(strict_types=1);

namespace App\Application\Console\Command;

use DateTimeZone;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use App\Application\Mail\Address;
use App\Application\Mail\Message;
use App\Application\Statistics\Period;
use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Component\Console\Command\Command;
use App\Application\Statistics\PeriodStatistics;
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
use App\Application\Statistics\ProjectStatisticsCalculatorInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery;

final class ConsentDecreaseNotifierCommand extends Command
{
	private QueryBusInterface $queryBus;

	private CommandBusInterface $commandBus;

	private ProjectStatisticsCalculatorInterface $projectStatisticsCalculator;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\Statistics\ProjectStatisticsCalculatorInterface $projectStatisticsCalculator
	 */
	public function __construct(QueryBusInterface $queryBus, CommandBusInterface $commandBus, ProjectStatisticsCalculatorInterface $projectStatisticsCalculator)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
		$this->projectStatisticsCalculator = $projectStatisticsCalculator;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		$this->setName('cmp:consent-decrease-notifier')
			->setDescription('Sends notifications about consent decrease for the previous day for all projects.');
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$logger = new ConsoleLogger($output);
		$timezone = new DateTimeZone('UTC');
		$currentPeriod = Period::create(
			new DateTimeImmutable('yesterday 00:00:00', $timezone),
			new DateTimeImmutable('yesterday 23:59:59', $timezone)
		);
		$previousPeriod = Period::create(
			new DateTimeImmutable('yesterday -7 days 00:00:00', $timezone),
			new DateTimeImmutable('yesterday -1 day 23:59:59', $timezone)
		);

		$projects = [];

		// find all projects
		foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
			assert($projectSelectOptionView instanceof ProjectSelectOptionView);

			$projectId = $projectSelectOptionView->id->toString();
			$projects[$projectId] = [
				'id' => $projectId,
				'name' => $projectSelectOptionView->name->value(),
				'allConsents' => 0,
				'percentageDiff' => 0,
			];
		}

		// fetch stats for all projects
		$projectIds = array_keys($projects);
		$allConsentStatistics = $this->projectStatisticsCalculator->calculateConsentStatistics($projectIds, $currentPeriod, $previousPeriod);

		// build stats for each project
		foreach ($projectIds as $projectId) {
			$totalConsentStatistics = $allConsentStatistics->get($projectId)->totalConsentsStatistics();

			$averagePeriodStatistics = PeriodStatistics::create(
				(int) round($totalConsentStatistics->previousValue() / 7),
				$totalConsentStatistics->currentValue()
			);

			if (0 < $averagePeriodStatistics->currentValue() && -90 < $averagePeriodStatistics->percentageDiff()) {
				$logger->info(sprintf(
					'The project %s has %d consents on the previous day (%d%% difference from the last 7 days average [%d]). Skipping...',
					$projects[$projectId]['name'],
					$averagePeriodStatistics->currentValue(),
					$averagePeriodStatistics->percentageDiff(),
					$averagePeriodStatistics->previousValue()
				));

				unset($projects[$projectId]);

				continue;
			}

			$logger->info(sprintf(
				'The project %s has %d consents on the previous day (%d%% difference from the last 7 days average [%d]). The project is rated as risky.',
				$projects[$projectId]['name'],
				$averagePeriodStatistics->currentValue(),
				$averagePeriodStatistics->percentageDiff(),
				$averagePeriodStatistics->previousValue()
			));

			$projects[$projectId]['allConsents'] = $averagePeriodStatistics->currentValue();
			$projects[$projectId]['percentageDiff'] = $averagePeriodStatistics->percentageDiff();
		}

		if (0 < count($projects)) {
			$this->processRiskyProjects($projects, $logger);
		}

		$output->writeln('OK');

		return 0;
	}

	/**
	 * @param array                    $projects
	 * @param \Psr\Log\LoggerInterface $logger
	 *
	 * @return void
	 */
	protected function processRiskyProjects(array $projects, LoggerInterface $logger): void
	{
		foreach ($this->queryBus->dispatch(FindNotificationReceiversByTypeAndProjectIdsQuery::create(NotificationType::CONSENT_DECREASED, array_keys($projects))) as $batch) {
			assert($batch instanceof Batch);

			foreach ($batch->results() as $notificationReceiverView) {
				assert($notificationReceiverView instanceof NotificationReceiverView);

				$this->notify($notificationReceiverView, $projects, $logger);
			}
		}
	}

	/**
	 * @param \App\ReadModel\User\NotificationReceiverView $notificationReceiverView
	 * @param array                                        $projects
	 * @param \Psr\Log\LoggerInterface                     $logger
	 *
	 * @return void
	 */
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

		$message = Message::create('~consent_decrease.latte', $notificationReceiverView->profileLocale->value())
			->withTo(Address::create($notificationReceiverView->emailAddress->value(), $notificationReceiverView->name->name()))
			->withArguments([
				'projects' => $userProjects,
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message));
	}
}
