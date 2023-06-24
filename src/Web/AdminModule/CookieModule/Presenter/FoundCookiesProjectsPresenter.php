<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Application\Acl\FoundCookiesProjectsResource;
use App\ReadModel\Project\FoundCookiesProjectsListingQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;

/**
 * @IsAllowed(resource=FoundCookiesProjectsResource::class, privilege=FoundCookiesProjectsResource::READ)
 */
final class FoundCookiesProjectsPresenter extends AdminPresenter
{
	private QueryBusInterface $queryBus;

	public function __construct(QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof FoundCookiesProjectsTemplate);

		$template->projects = $this->queryBus->dispatch(FoundCookiesProjectsListingQuery::create());
	}
}
