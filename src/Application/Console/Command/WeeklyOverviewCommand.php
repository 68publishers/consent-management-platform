<?php

declare(strict_types=1);

namespace App\Application\Console\Command;

use App\Application\Mail\Address;
use App\Application\Mail\Command\SendMailCommand;
use App\Application\Mail\Message;
use App\Application\Statistics\Period;
use App\Application\Statistics\ProjectStatisticsCalculatorInterface;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\ValueObject\NotificationType;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\ReadModel\User\FindNotificationReceiversByTypeAndProjectIdsQuery;
use App\ReadModel\User\NotificationReceiverView;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class WeeklyOverviewCommand extends Command
{
    private QueryBusInterface $queryBus;

    private CommandBusInterface $commandBus;

    private ProjectStatisticsCalculatorInterface $projectStatisticsCalculator;

    public function __construct(QueryBusInterface $queryBus, CommandBusInterface $commandBus, ProjectStatisticsCalculatorInterface $projectStatisticsCalculator)
    {
        parent::__construct();

        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->projectStatisticsCalculator = $projectStatisticsCalculator;
    }

    protected function configure(): void
    {
        $this->setName('cmp:weekly-overview')
            ->setDescription('Sends weekly report with consent statistics for all projects.');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $timezone = new DateTimeZone('UTC');
        $period = Period::create(
            new DateTimeImmutable('last week monday 00:00:00', $timezone),
            new DateTimeImmutable('last week sunday 23:59:59', $timezone),
        );

        $projects = [];

        // find all projects
        foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
            assert($projectSelectOptionView instanceof ProjectSelectOptionView);

            $projectId = $projectSelectOptionView->id->toString();
            $projects[$projectId] = [
                'id' => $projectId,
                'name' => $projectSelectOptionView->name->value(),
            ];
        }

        // build stats for each project
        foreach (array_keys($projects) as $projectId) {
            $consentStatistics = $this->projectStatisticsCalculator->calculateConsentStatistics($projectId, $period);
            $cookieStatistics = $this->projectStatisticsCalculator->calculateCookieStatistics($projectId, $period->endDate());
            $lastConsentDate = $this->projectStatisticsCalculator->calculateLastConsentDate($projectId, $period->endDate());
            $cookieSuggestionStatistics = $this->projectStatisticsCalculator->calculateCookieSuggestionStatistics($projectId);

            $projects[$projectId] = array_merge($projects[$projectId], [
                'uniqueConsents' => [
                    'value' => $consentStatistics->uniqueConsentsStatistics()->currentValue(),
                    'percentageDiff' => $consentStatistics->uniqueConsentsStatistics()->percentageDiff(),
                ],
                'uniquePositive' => [
                    'value' => $consentStatistics->uniqueConsentsPositivityStatistics()->currentValue(),
                    'percentageDiff' => $consentStatistics->uniqueConsentsPositivityStatistics()->percentageDiff(),
                ],
                'lastConsent' => [
                    'value' => $lastConsentDate?->format(DateTimeInterface::ATOM),
                    'formattedValue' => $lastConsentDate?->format('j.n.Y H:i:s'),
                ],
                'providers' => [
                    'value' => $cookieStatistics->numberOfProviders(),
                ],
                'cookies' => [
                    'commonValue' => $cookieStatistics->numberOfCommonCookies(),
                    'privateValue' => $cookieStatistics->numberOfPrivateCookies(),
                ],
                'cookieSuggestions' => [
                    'enabled' => null !== $cookieSuggestionStatistics,
                    'missing' => null !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->missing : 0,
                    'unassociated' => null !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->unassociated : 0,
                    'problematic' => null !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->problematic : 0,
                    'unproblematic' => null !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->unproblematic : 0,
                    'ignored' => null !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->ignored : 0,
                ],
            ]);
        }

        // find all receivers and send mail for associated projects
        foreach ($this->queryBus->dispatch(FindNotificationReceiversByTypeAndProjectIdsQuery::create(NotificationType::WEEKLY_OVERVIEW)) as $batch) {
            assert($batch instanceof Batch);

            foreach ($batch->results() as $notificationReceiverView) {
                assert($notificationReceiverView instanceof NotificationReceiverView);

                $this->notify($notificationReceiverView, $projects, $period, $logger);
            }
        }

        $output->writeln('OK');

        return 0;
    }

    private function notify(NotificationReceiverView $notificationReceiverView, array $projects, Period $period, LoggerInterface $logger): void
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
            'Sending weekly overview to %s ...',
            $notificationReceiverView->emailAddress->value(),
        ));

        $message = Message::create('~weekly_overview.latte', $notificationReceiverView->profileLocale->value())
            ->withTo(Address::create($notificationReceiverView->emailAddress->value(), $notificationReceiverView->name->name()))
            ->withArguments([
                'projects' => $userProjects,
                'formattedStartDate' => $period->startDate()->format('j.n.Y'),
                'formattedEndDate' => $period->endDate()->format('j.n.Y'),
            ]);

        $this->commandBus->dispatch(SendMailCommand::create($message));
    }
}
