<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\Environment;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

final class Consent implements AggregateRootInterface
{
    use AggregateRootTrait;

    private ConsentId $id;

    private ProjectId $projectId;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $lastUpdateAt;

    private UserIdentifier $userIdentifier;

    private ?Checksum $settingsChecksum = null;

    private Consents $consents;

    private Attributes $attributes;

    private ?Environment $environment = null;

    public static function create(StoreConsentCommand $command, CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists): self
    {
        $consentId = ConsentId::new();
        $projectId = ProjectId::fromString($command->projectId());
        $userIdentifier = UserIdentifier::fromValue($command->userIdentifier());
        $settingsChecksum = null !== $command->settingsChecksum() ? Checksum::fromValue($command->settingsChecksum()) : null;
        $consents = Consents::fromArray($command->consents());
        $attributes = Attributes::fromArray($command->attributes());
        $environment = null !== $command->environment() ? Environment::fromValue($command->environment()) : null;

        $checkUserIdentifierNotExists($userIdentifier, $projectId);

        $consent = new self();

        $consent->recordThat(ConsentCreated::create(
            consentId: $consentId,
            projectId: $projectId,
            userIdentifier: $userIdentifier,
            settingsChecksum: $settingsChecksum,
            consents: $consents,
            attributes: $attributes,
            environment: $environment,
            createdAt: $command->createdAt(),
        ));

        return $consent;
    }

    public function update(StoreConsentCommand $command): void
    {
        $consents = Consents::fromArray($command->consents());
        $attributes = Attributes::fromArray($command->attributes());
        $settingsChecksum = null !== $command->settingsChecksum() ? Checksum::fromValue($command->settingsChecksum()) : null;
        $environment = null !== $command->environment() ? Environment::fromValue($command->environment()) : null;

        if (!$this->consents->equals($consents)
            || !$this->attributes->equals($attributes)
            || !$this->areChecksumsEquals($settingsChecksum)
            || !$this->areEnvironmentsEquals($environment)
        ) {
            $this->recordThat(ConsentUpdated::create(
                consentId: $this->id,
                projectId: $this->projectId,
                settingsChecksum: $settingsChecksum,
                consents: $consents,
                attributes: $attributes,
                environment: $environment,
                createdAt: $command->createdAt(),
            ));
        }
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    protected function whenConsentCreated(ConsentCreated $event): void
    {
        $this->id = $event->consentId();
        $this->projectId = $event->projectId();
        $this->createdAt = $event->createdAt();
        $this->lastUpdateAt = $event->createdAt();
        $this->userIdentifier = $event->userIdentifier();
        $this->settingsChecksum = $event->settingsChecksum();
        $this->consents = $event->consents();
        $this->attributes = $event->attributes();
        $this->environment = $event->environment();
    }

    protected function whenConsentUpdated(ConsentUpdated $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->settingsChecksum = $event->settingsChecksum();
        $this->consents = $event->consents();
        $this->attributes = $event->attributes();
        $this->environment = $event->environment();
    }

    private function areChecksumsEquals(?Checksum $checksum): bool
    {
        if (null === $this->settingsChecksum && null === $checksum) {
            return true;
        }

        if (null === $this->settingsChecksum || null === $checksum) {
            return false;
        }

        return $this->settingsChecksum->equals($checksum);
    }

    private function areEnvironmentsEquals(?Environment $environment): bool
    {
        if (null === $this->environment && null === $environment) {
            return true;
        }

        if (null === $this->environment || null === $environment) {
            return false;
        }

        return $this->environment->equals($environment);
    }
}
