<?php

declare(strict_types=1);

namespace App\Subscribers\GlobalSettings;

use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Event\CrawlerSettingsChanged;
use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\LocalizationSettingsChanged;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;

final class RefreshGlobalSettingsWhenChanged implements EventHandlerInterface, MessageSubscriberInterface
{
	private GlobalSettingsInterface $globalSettings;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface $globalSettings
	 */
	public function __construct(GlobalSettingsInterface $globalSettings)
	{
		$this->globalSettings = $globalSettings;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getHandledMessages(): iterable
	{
		yield GlobalSettingsCreated::class;
		yield LocalizationSettingsChanged::class;
		yield ApiCacheSettingsChanged::class;
		yield CrawlerSettingsChanged::class;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void
	{
		$this->globalSettings->refresh();
	}
}
