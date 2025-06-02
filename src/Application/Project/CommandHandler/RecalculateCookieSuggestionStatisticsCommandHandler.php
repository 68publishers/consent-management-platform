<?php

declare(strict_types=1);

namespace App\Application\Project\CommandHandler;

use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;
use App\Application\Project\Command\RecalculateCookieSuggestionStatisticsCommand;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use function count;

#[AsMessageHandler(bus: 'command')]
final class RecalculateCookieSuggestionStatisticsCommandHandler implements CommandHandlerInterface, BatchHandlerInterface
{
    use BatchHandlerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CookieSuggestionsStoreInterface $cookieSuggestionsStore,
    ) {}

    public function __invoke(RecalculateCookieSuggestionStatisticsCommand $command, ?Acknowledger $ack = null): void
    {
        $this->handle($command, $ack);
    }

    private function shouldFlush(): bool
    {
        return 50 <= count($this->jobs);
    }

    /**
     * @param list<array{0: RecalculateCookieSuggestionStatisticsCommand, 1: Acknowledger}> $jobs
     *
     * @throws Exception
     */
    private function process(array $jobs): void
    {
        $projectIds = [];

        foreach ($jobs as [$command, $ack]) {
            assert($command instanceof RecalculateCookieSuggestionStatisticsCommand);
            assert($ack instanceof Acknowledger);

            $projectIds[] = $command->projectIds();

            $ack->ack(true);
        }

        $projectIds = array_unique(
            array_merge(
                ...$projectIds,
            ),
        );

        if (0 >= count($projectIds)) {
            return;
        }

        foreach ($projectIds as $projectId) {
            $this->recalculateProject($projectId);
        }
    }

    /**
     * @throws Exception
     */
    private function recalculateProject(string $projectId): void
    {
        $results = $this->cookieSuggestionsStore->resolveCookieSuggestions($projectId);

        $missing = count($results->getSuggestionsByType(MissingCookieSuggestion::class));
        $unassociated = count($results->getSuggestionsByType(UnassociatedCookieSuggestion::class));
        $problematic = count($results->getSuggestionsByType(ProblematicCookieSuggestion::class));
        $unproblematic = count($results->getSuggestionsByType(UnproblematicCookieSuggestion::class));
        $ignored = count($results->getSuggestionsByType(IgnoredCookieSuggestion::class));

        $latestOccurrence = $results->getLatestOccurrence();
        $latestFoundAt = $latestOccurrence?->lastFoundAt;

        $allSuggestions = $results->getSuggestions();
        $total = count($allSuggestions);
        $totalWithoutVirtual = 0;

        foreach ($allSuggestions as $suggestion) {
            if (!$suggestion->isVirtual()) {
                ++$totalWithoutVirtual;
            }
        }

        $values = [
            'missing' => $missing,
            'unassociated' => $unassociated,
            'problematic' => $problematic,
            'unproblematic' => $unproblematic,
            'ignored' => $ignored,
            'total' => $total,
            'total_without_virtual' => $totalWithoutVirtual,
            'latest_found_at' => ':latestFoundAt',
        ];

        $connection = $this->em->getConnection();
        $existingProjectId = $connection->createQueryBuilder()
            ->select('p.id')
            ->from('project_cookie_suggestion_statistics', 'p')
            ->where('p.project_id = :projectId')
            ->setParameter('projectId', $projectId, Types::GUID)
            ->setMaxResults(1)
            ->fetchOne();

        if (!$existingProjectId) {
            $values['project_id'] = ':projectId';

            $connection->createQueryBuilder()
                ->insert('project_cookie_suggestion_statistics')
                ->values($values)
                ->setParameter('projectId', $projectId, Types::GUID)
                ->setParameter('latestFoundAt', $latestFoundAt, Types::DATETIME_IMMUTABLE)
                ->executeStatement();
        } else {
            $updateQuery = $connection->createQueryBuilder()
                ->update('project_cookie_suggestion_statistics')
                ->where('project_id = :projectId')
                ->setParameter('latestFoundAt', $latestFoundAt, Types::DATETIME_IMMUTABLE)
                ->setParameter('projectId', $projectId, Types::GUID);

            foreach ($values as $k => $v) {
                $updateQuery->set($k, $v);
            }

            $updateQuery->executeStatement();
        }
    }
}
