<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\EmailAddress;

final class NotificationReceiverView extends AbstractView
{
	public EmailAddress $emailAddress;

	public Name $name;

	public Locale $profileLocale;

	/** @var \App\Domain\Project\ValueObject\ProjectId[] */
	public array $projectIds;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'emailAddress' => $this->emailAddress->value(),
			'name' => $this->name->name(),
			'profileLocale' => $this->profileLocale->value(),
			'projectIds' => array_map(static fn (ProjectId $projectId): string => $projectId->toString(), $this->projectIds),
		];
	}
}
