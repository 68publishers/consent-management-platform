<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Nette\Application\BadRequestException;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Application\Acl\FoundCookiesProjectsResource;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;
use App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList\ProjectCookieSuggestionListControl;
use App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList\ProjectCookieSuggestionListControlFactoryInterface;

/**
 * @IsAllowed(resource=FoundCookiesProjectsResource::class, privilege=FoundCookiesProjectsResource::READ)
 */
final class FoundCookiesProjectsPresenter extends AdminPresenter
{
	private ProjectCookieSuggestionListControlFactoryInterface $projectCookieSuggestionListControlFactory;

	public function __construct(ProjectCookieSuggestionListControlFactoryInterface $projectCookieSuggestionListControlFactory)
	{
		parent::__construct();

		$this->projectCookieSuggestionListControlFactory = $projectCookieSuggestionListControlFactory;
	}

	/**
	 * @throws IdentityException
	 * @throws BadRequestException
	 */
	protected function startup(): void
	{
		parent::startup();

		if (!$this->globalSettings->crawlerSettings()->enabled()) {
			$this->error('Crawler is disabled.');
		}
	}

	protected function createComponentList(): ProjectCookieSuggestionListControl
	{
		return $this->projectCookieSuggestionListControlFactory->create();
	}
}
