<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\FoundCookiesProjectsResource;
use App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList\ProjectCookieSuggestionListControl;
use App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList\ProjectCookieSuggestionListControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use Nette\Application\BadRequestException;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;

#[Allowed(resource: FoundCookiesProjectsResource::class, privilege: FoundCookiesProjectsResource::READ)]
final class FoundCookiesProjectsPresenter extends AdminPresenter
{
    public function __construct(
        private readonly ProjectCookieSuggestionListControlFactoryInterface $projectCookieSuggestionListControlFactory,
    ) {
        parent::__construct();
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
