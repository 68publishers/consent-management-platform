<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Consents;
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

    /**
     * @return static
     */
    public static function create(StoreConsentCommand $command, CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists): self
    {
        $consentId = ConsentId::new();
        $projectId = ProjectId::fromString($command->projectId());
        $userIdentifier = UserIdentifier::fromValue($command->userIdentifier());
        $settingsChecksum = null !== $command->settingsChecksum() ? Checksum::fromValue($command->settingsChecksum()) : null;
        $consents = Consents::fromArray($command->consents());
        $attributes = Attributes::fromArray($command->attributes());

        $checkUserIdentifierNotExists($userIdentifier, $projectId);

        $consent = new self();

        $consent->recordThat(ConsentCreated::create($consentId, $projectId, $userIdentifier, $settingsChecksum, $consents, $attributes, $command->createdAt()));

        return $consent;
    }

    public function update(StoreConsentCommand $command): void
    {
        $consents = Consents::fromArray($command->consents());
        $attributes = Attributes::fromArray($command->attributes());
        $settingsChecksum = null !== $command->settingsChecksum() ? Checksum::fromValue($command->settingsChecksum()) : null;

        if (!$this->consents->equals($consents) || !$this->attributes->equals($attributes) || !$this->areChecksumsEquals($settingsChecksum)) {
            $this->recordThat(ConsentUpdated::create($this->id, $this->projectId, $settingsChecksum, $consents, $attributes, $command->createdAt()));
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
    }

    protected function whenConsentUpdated(ConsentUpdated $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->settingsChecksum = $event->settingsChecksum();
        $this->consents = $event->consents();
        $this->attributes = $event->attributes();
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
}
