<?php

declare(strict_types=1);

namespace App\Projection\Consent;

use App\Domain\Consent\Consent;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\Environment;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Category\FindAllOptionalCategoryCodesQuery;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ProjectionBundle\Projection\AbstractProjection;
use SixtyEightPublishers\ProjectionBundle\Projection\EventDefinition;
use SixtyEightPublishers\ProjectionBundle\ProjectionModel\ProjectionModelLocatorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class ConsentStatisticsProjection extends AbstractProjection
{
    public function __construct(
        ProjectionModelLocatorInterface $projectionModelLocator,
        private readonly QueryBusInterface $queryBus,
    ) {
        parent::__construct($projectionModelLocator);
    }

    public static function projectionName(): string
    {
        return 'consent_statistics';
    }

    public static function defineEvents(): iterable
    {
        yield new EventDefinition(Consent::class, ConsentCreated::class);
        yield new EventDefinition(Consent::class, ConsentUpdated::class);
    }

    #[AsMessageHandler(bus: 'projection', fromTransport: 'consent_statistics')]
    public function whenConsentCreated(ConsentCreated $event): void
    {
        $this->insertRow($event->projectId(), $event->consentId(), $event->createdAt(), $event->consents(), $event->environment());
    }

    #[AsMessageHandler(bus: 'projection', fromTransport: 'consent_statistics')]
    public function whenConsentUpdated(ConsentUpdated $event): void
    {
        $this->insertRow($event->projectId(), $event->consentId(), $event->createdAt(), $event->consents(), $event->environment());
    }

    private function insertRow(ProjectId $projectId, ConsentId $consentId, DateTimeImmutable $createdAt, Consents $consents, Environment $environment): void
    {
        $categoryCodes = $this->queryBus->dispatch(FindAllOptionalCategoryCodesQuery::create());

        $this->projectionModel()->insert([
            'project_id' => $projectId->toString(),
            'consent_id' => $consentId->toString(),
            'created_at' => $createdAt,
            'environment' => $environment->value(),
            'positive_count' => $consents->positiveCount($categoryCodes),
            'negative_count' => $consents->negativeCount($categoryCodes),
        ]);
    }
}
